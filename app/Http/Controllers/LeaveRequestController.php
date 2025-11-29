<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::where('user_id', Auth::id());
        if ($request->filled('jenis_cuti')) $query->where('jenis_cuti', $request->jenis_cuti);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_mulai', $request->bulan)->whereYear('tanggal_mulai', $request->tahun);
        }
        if ($request->filled('tgl_pengajuan')) $query->whereDate('created_at', $request->tgl_pengajuan);
        $leaves = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('leaves.index', compact('leaves'));
    }

    public function create() { return view('leaves.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required',
            'bukti_sakit' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'alamat_selama_cuti' => 'required|string|max:255',
            'nomor_darurat' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);
        
        $totalHari = 0;
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            if (!$curr->isWeekend()) $totalHari++;
            $curr->addDay();
        }

        if ($request->jenis_cuti == 'tahunan') {
            $today = Carbon::now()->startOfDay();
            $minDate = $today->copy()->addDays(3);
            if ($startDate->lt($minDate)) {
                return back()->withErrors(['tanggal_mulai' => 'Untuk Cuti Tahunan, pengajuan minimal H-3.'])->withInput();
            }
            if ($user->created_at->diffInYears(Carbon::now()) < 1) {
                return back()->withErrors(['jenis_cuti' => 'Maaf, masa kerja belum 1 tahun.'])->withInput();
            }
            if ($user->kuota_cuti < $totalHari) {
                return back()->withErrors(['jenis_cuti' => 'Sisa kuota tidak mencukupi.'])->withInput();
            }
        }

        $isOverlapping = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved_leader', 'approved'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                  ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->where('tanggal_mulai', '<=', $startDate)->where('tanggal_selesai', '>=', $endDate);
                  });
            })->exists();

        if ($isOverlapping) return back()->withErrors(['tanggal_mulai' => 'Tanggal cuti overlap.'])->withInput();

        $filePath = $request->hasFile('bukti_sakit') ? $request->file('bukti_sakit')->store('surat_dokter', 'public') : null;
        
        $initialStatus = 'pending';
        $leaderApprovedAt = null;
        $catatanLeader = null;
        if ($user->role === 'ketua_divisi') {
            $initialStatus = 'approved_leader'; 
            $leaderApprovedAt = now();          
            $catatanLeader = 'Pengajuan langsung oleh Ketua Divisi.';
        }

        LeaveRequest::create([
            'user_id' => $user->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_hari' => $totalHari,
            'alasan' => $request->alasan,
            'bukti_sakit' => $filePath,
            'status' => $initialStatus, 
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'nomor_darurat' => $request->nomor_darurat,
            'catatan_leader' => $catatanLeader, 
            'approved_leader_at' => $leaderApprovedAt,
        ]);

        if ($request->jenis_cuti == 'tahunan') $user->decrement('kuota_cuti', $totalHari);

        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();
        if ($user->id !== $leaveRequest->user_id && $user->role !== 'admin' && $user->role !== 'hrd' && 
            ($user->role !== 'ketua_divisi' || $user->divisiKetua->id !== $leaveRequest->user->divisi_id)) { 
            abort(403);
        }
        return view('leaves.show', ['leave' => $leaveRequest]);
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        if (Auth::id() !== $leaveRequest->user_id) abort(403);
        if (!in_array($leaveRequest->status, ['pending', 'approved_leader'])) return back()->with('error', 'Tidak bisa dibatalkan.');

        $request->validate(['alasan_pembatalan' => 'required|string']);

        if ($leaveRequest->jenis_cuti == 'tahunan') $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
        if ($leaveRequest->bukti_sakit) Storage::disk('public')->delete($leaveRequest->bukti_sakit);

        $leaveRequest->update(['status' => 'cancelled', 'alasan_pembatalan' => $request->alasan_pembatalan]);
        return back()->with('success', 'Pengajuan dibatalkan. Kuota dikembalikan.');
    }

    public function downloadPdf(LeaveRequest $leaveRequest)
    {
        if (Auth::id() !== $leaveRequest->user_id && !in_array(Auth::user()->role, ['hrd', 'ketua_divisi', 'admin'])) abort(403);
        if ($leaveRequest->status !== 'approved') return back()->with('error', 'Hanya cuti disetujui yang dapat diunduh.');

        $leave = $leaveRequest; 
        $hrdManager = \App\Models\User::where('role', 'hrd')->first();
        $pdf = Pdf::loadView('leaves.pdf', compact('leave', 'hrdManager'));
        return $pdf->download('Surat_Izin_Cuti_' . $leaveRequest->user->name . '.pdf');
    }

    public function leaderIndex()
    {
        if (Auth::user()->role !== 'ketua_divisi') abort(403);
        $managedDivisi = Auth::user()->divisiKetua;
        $pendingRequests = LeaveRequest::where('status', 'pending')
            ->whereHas('user', function ($q) use ($managedDivisi) {
                $q->where('divisi_id', $managedDivisi->id ?? 0)->where('id', '!=', Auth::id());
            })->with('user.divisi')->orderBy('created_at', 'asc')->get();
        return view('leaves.leader_index', compact('pendingRequests'));
    }

    public function leaderAction(Request $request, LeaveRequest $leaveRequest)
    {
        if (Auth::user()->role !== 'ketua_divisi') abort(403);
        $action = $request->input('action');
        
        if ($action === 'approve') {
            $leaveRequest->update([
                'status' => 'approved_leader',
                'catatan_leader' => $request->input('catatan'),
                'approved_leader_at' => now(), 
            ]);
        } elseif ($action === 'reject') {
            $request->validate(['catatan' => 'required|min:5']);
            if ($leaveRequest->jenis_cuti == 'tahunan') $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
            $leaveRequest->update(['status' => 'rejected', 'catatan_penolakan' => 'Ditolak Ketua Divisi: ' . $request->input('catatan')]);
        }
        return redirect()->route('leader.leaves.index')->with('success', 'Diproses.');
    }

    public function hrdIndex()
    {
        if (Auth::user()->role !== 'hrd') abort(403);
        $pendingRequests = LeaveRequest::with(['user', 'user.divisi'])->where('status', 'approved_leader')->orderBy('created_at', 'asc')->get();
        return view('leaves.hrd_index', compact('pendingRequests'));
    }

    public function hrdAction(Request $request, LeaveRequest $leaveRequest)
    {
        if (Auth::user()->role !== 'hrd') abort(403);
        $action = $request->input('action');
        
        if ($action === 'approve') {
            $leaveRequest->update([
                'status' => 'approved',
                'approved_hrd_at' => now(),
                'catatan_hrd' => $request->input('catatan')
            ]);
            $msg = 'Pengajuan disetujui (Final).';
        } elseif ($action === 'reject') {
            $request->validate(['catatan' => 'required|min:10']);
            if ($leaveRequest->jenis_cuti == 'tahunan' && $leaveRequest->status !== 'rejected') {
                $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
            }
            $leaveRequest->update([
                'status' => 'rejected',
                'catatan_penolakan' => 'Ditolak HRD: ' . $request->input('catatan'),
                'approved_hrd_at' => now(),
            ]);
            $msg = 'Pengajuan ditolak.';
        }
        return redirect()->route('hrd.leaves.index')->with('success', $msg ?? 'Diproses.');
    }

    public function hrdBulkAction(Request $request)
    {
        if (Auth::user()->role !== 'hrd') abort(403);

        $request->validate([
            'ids' => 'required|string', 
            'bulk_action' => 'required|in:approve,reject',
            'bulk_catatan' => 'nullable|string'
        ]);

        $ids = explode(',', $request->ids);
        $action = $request->bulk_action;
        $catatan = $request->bulk_catatan;

        if ($action === 'reject' && (!is_string($catatan) || strlen(trim($catatan)) < 10)) {
            return back()->with('error', 'Untuk penolakan massal, catatan wajib diisi minimal 10 karakter.');
        }

        DB::beginTransaction();
        try {
            $requests = LeaveRequest::whereIn('id', $ids)->where('status', 'approved_leader')->get();
            $count = 0;

            foreach ($requests as $req) {
                if ($action === 'approve') {
                    $req->update([
                        'status' => 'approved',
                        'approved_hrd_at' => now(),
                        'catatan_hrd' => 'Disetujui Massal (Bulk Action).',
                    ]);
                } else {
                    if ($req->jenis_cuti == 'tahunan') {
                        $req->user->increment('kuota_cuti', $req->total_hari);
                    }
                    $req->update([
                        'status' => 'rejected',
                        'catatan_penolakan' => 'Ditolak Massal HRD: ' . $catatan,
                        'approved_hrd_at' => now(),
                    ]);
                }
                $count++;
            }
            DB::commit();
            return redirect()->route('hrd.leaves.index')->with('success', "$count pengajuan berhasil diproses secara massal.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses data.');
        }
    }

    public function report()
    {
        if (Auth::user()->role !== 'hrd') abort(403);
        $leaves = LeaveRequest::with('user.divisi')->orderBy('created_at', 'desc')->get();
        return view('leaves.report', compact('leaves'));
    }
}