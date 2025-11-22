<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveRequestController extends Controller
{
    // ==========================================
    // 1. FITUR PENGAJUAN (USER & KETUA DIVISI)
    // ==========================================

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        // Validasi Input Dasar
        $request->validate([
            'jenis_cuti' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required',
            'bukti_sakit' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);
        $today = Carbon::today();

        // --- ATURAN CUTI TAHUNAN ---
        // Pengajuan minimal H-3
        if ($request->jenis_cuti == 'tahunan') {
            $minDate = $today->copy()->addDays(3);
            if ($startDate->lt($minDate)) {
                return back()->withErrors(['tanggal_mulai' => 'Cuti tahunan wajib diajukan minimal H-3 (Paling cepat: ' . $minDate->format('d-m-Y') . ').'])->withInput();
            }
        }

        // --- ATURAN CUTI SAKIT ---
        // Maksimal diajukan 3 hari setelah sakit dimulai (Backdate limit)
        if ($request->jenis_cuti == 'sakit') {
            $maxBackDate = $today->copy()->subDays(3);
            if ($startDate->lt($maxBackDate)) {
                return back()->withErrors(['tanggal_mulai' => 'Cuti sakit maksimal diajukan 3 hari setelah tanggal mulai sakit.'])->withInput();
            }
        }

        // --- VALIDASI OVERLAP ---
        $overlap = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved_leader', 'approved'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
                      ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('tanggal_mulai', '<=', $startDate)
                            ->where('tanggal_selesai', '>=', $endDate);
                      });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['tanggal_mulai' => 'Anda sudah memiliki pengajuan cuti pada rentang tanggal tersebut.'])->withInput();
        }

        // --- HITUNG HARI KERJA (Senin-Jumat) ---
        $totalHari = 0;
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if (!$currentDate->isWeekend()) {
                $totalHari++;
            }
            $currentDate->addDay();
        }

        if ($totalHari == 0) {
            return back()->withErrors(['tanggal_mulai' => 'Rentang tanggal hanya berisi hari libur (Sabtu/Minggu).'])->withInput();
        }

        // --- CEK KUOTA (TAHUNAN) ---
        if ($request->jenis_cuti == 'tahunan') {
            if ($user->kuota_cuti < $totalHari) {
                return back()->withErrors(['jenis_cuti' => 'Sisa kuota cuti tidak mencukupi.'])->withInput();
            }
        }

        // --- UPLOAD BUKTI SAKIT ---
        $filePath = null;
        if ($request->jenis_cuti == 'sakit') {
            if (!$request->hasFile('bukti_sakit')) {
                return back()->withErrors(['bukti_sakit' => 'Wajib upload surat dokter.'])->withInput();
            }
            $filePath = $request->file('bukti_sakit')->store('surat_dokter', 'public');
        }

        // --- SIMPAN DATA ---
        LeaveRequest::create([
            'user_id' => $user->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_hari' => $totalHari,
            'alasan' => $request->alasan,
            'bukti_sakit' => $filePath,
            'status' => 'pending',
        ]);

        // Potong kuota di awal (akan dikembalikan jika direject/cancel)
        if ($request->jenis_cuti == 'tahunan') {
            $user->decrement('kuota_cuti', $totalHari);
        }

        return redirect()->route('dashboard')->with('success', 'Pengajuan cuti berhasil dikirim!');
    }

    public function cancel(LeaveRequest $leaveRequest)
    {
        if (Auth::id() !== $leaveRequest->user_id) abort(403);
        if ($leaveRequest->status !== 'pending') return back()->with('error', 'Hanya status pending yang bisa dibatalkan.');

        if ($leaveRequest->jenis_cuti == 'tahunan') {
            $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
        }

        if ($leaveRequest->bukti_sakit) {
            Storage::disk('public')->delete($leaveRequest->bukti_sakit);
        }

        $leaveRequest->update(['status' => 'cancelled']);
        return redirect()->route('dashboard')->with('success', 'Pengajuan dibatalkan, kuota dikembalikan.');
    }

    // ==========================================
    // 2. FITUR APPROVAL KETUA DIVISI (ALUR 1)
    // ==========================================

    public function leaderIndex()
    {
        $leader = Auth::user();
        
        // STRICT CHECK: Admin tidak boleh akses
        if ($leader->role !== 'ketua_divisi') {
            abort(403, 'Halaman ini khusus Ketua Divisi.');
        }

        // Cari ID divisi yang dipimpin oleh user ini
        // Menggunakan relasi 'divisiKetua' yang ada di Model User
        $managedDivisi = $leader->divisiKetua; 

        if (!$managedDivisi) {
            // Jika user rolenya ketua_divisi tapi belum diassign ke divisi mana pun
            return view('leaves.leader_index', ['pendingRequests' => collect([])]);
        }

        // Ambil pengajuan HANYA dari anggota divisi tersebut
        $pendingRequests = LeaveRequest::where('status', 'pending')
            ->whereHas('user', function ($query) use ($managedDivisi, $leader) {
                $query->where('divisi_id', $managedDivisi->id) // User harus satu divisi
                      ->where('id', '!=', $leader->id);        // User bukan si ketua itu sendiri
            })
            ->with('user.divisi')
            ->get();

        return view('leaves.leader_index', compact('pendingRequests'));
    }

    public function leaderAction(Request $request, LeaveRequest $leaveRequest)
    {
        // STRICT CHECK: Hanya Ketua Divisi
        if (Auth::user()->role !== 'ketua_divisi') abort(403);

        // STRICT CHECK: Pastikan yang diapprove adalah anggota divisinya sendiri
        if ($leaveRequest->user->divisi_id !== Auth::user()->divisiKetua->id) {
            abort(403, 'Anda tidak berhak memproses cuti dari divisi lain.');
        }

        $action = $request->input('action');
        $catatan = $request->input('catatan');

        if ($action === 'approve') {
            // ALUR 1: Pending -> Approved by Leader -> Lanjut ke HRD
            $leaveRequest->update([
                'status' => 'approved_leader', 
                'catatan_leader' => $catatan
            ]);
            $message = 'Disetujui. Pengajuan diteruskan ke HRD untuk finalisasi.';
        } elseif ($action === 'reject') {
            $request->validate(['catatan' => 'required|min:5']);
            
            if ($leaveRequest->jenis_cuti == 'tahunan') {
                $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
            }
            
            $leaveRequest->update([
                'status' => 'rejected', 
                'catatan_penolakan' => 'Ditolak Ketua Divisi: ' . $catatan
            ]);
            $message = 'Pengajuan ditolak.';
        }

        return redirect()->route('leader.leaves.index')->with('success', $message ?? '');
    }

    // ==========================================
    // 3. FITUR APPROVAL HRD (FINAL)
    // ==========================================

    public function hrdIndex()
    {
        // STRICT CHECK: Hanya HRD
        if (Auth::user()->role !== 'hrd') abort(403);

        // HRD Melihat:
        // 1. Alur 1: Status 'approved_leader' (Dari Karyawan -> Leader -> HRD)
        // 2. Alur 2: Status 'pending' TAPI dari User Role 'ketua_divisi' (Langsung ke HRD)
        
        $pendingRequests = LeaveRequest::with(['user', 'user.divisi'])
            ->where(function($q) {
                // Kondisi 1: Sudah di-ACC Leader
                $q->where('status', 'approved_leader')
                // Kondisi 2: Pending tapi dari Ketua Divisi
                  ->orWhere(function($subQ) {
                      $subQ->where('status', 'pending')
                           ->whereHas('user', function($userQ) {
                               $userQ->where('role', 'ketua_divisi');
                           });
                  });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leaves.hrd_index', compact('pendingRequests'));
    }

    public function hrdAction(Request $request, LeaveRequest $leaveRequest)
    {
        if (Auth::user()->role !== 'hrd') abort(403);

        $action = $request->input('action');
        $catatan = $request->input('catatan');

        if ($action === 'approve') {
            // Final Approval
            $leaveRequest->update(['status' => 'approved']);
            $message = 'Pengajuan disetujui sepenuhnya (Final).';
        } elseif ($action === 'reject') {
            $request->validate(['catatan' => 'required|min:5']);

            if ($leaveRequest->jenis_cuti == 'tahunan') {
                $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
            }

            $leaveRequest->update([
                'status' => 'rejected',
                'catatan_penolakan' => 'Ditolak HRD: ' . $catatan
            ]);
            $message = 'Pengajuan ditolak.';
        }

        return redirect()->route('hrd.leaves.index')->with('success', $message ?? '');
    }

    // ==========================================
    // 4. FITUR LAPORAN (REPORT)
    // ==========================================

    public function report()
    {
        // Admin Boleh Lihat Laporan, TAPI tidak boleh approve
        // HRD Boleh Lihat Laporan
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hrd') {
            abort(403);
        }

        $leaves = LeaveRequest::with(['user', 'user.divisi'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('leaves.report', compact('leaves'));
    }

    // ==========================================
    // 5. DOWNLOAD PDF
    // ==========================================
    
    public function downloadPdf(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();
        
        // Validasi Akses PDF: Pemilik, HRD, atau Ketua Divisi ybs
        $isOwner = $user->id === $leaveRequest->user_id;
        $isHRD = $user->role === 'hrd';
        $isLeader = $user->role === 'ketua_divisi' && $user->divisiKetua?->id === $leaveRequest->user->divisi_id;

        if (!$isOwner && !$isHRD && !$isLeader) {
            abort(403);
        }

        if ($leaveRequest->status !== 'approved') {
            return back()->with('error', 'Surat hanya bisa dicetak setelah status Approved.');
        }

        $pdf = Pdf::loadView('leaves.print_pdf', compact('leaveRequest'));
        return $pdf->download('Surat_Cuti_' . $leaveRequest->user->name . '.pdf');
    }
}