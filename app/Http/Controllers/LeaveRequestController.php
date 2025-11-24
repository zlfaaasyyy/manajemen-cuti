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
        
        // Filter Jenis
        if ($request->filled('jenis_cuti')) {
            $query->where('jenis_cuti', $request->jenis_cuti);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Bulan & Tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_mulai', $request->bulan)
                  ->whereYear('tanggal_mulai', $request->tahun);
        }

        // Filter Tanggal Pengajuan
        if ($request->filled('tgl_pengajuan')) {
            $query->whereDate('created_at', $request->tgl_pengajuan);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'jenis_cuti' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required',
            'bukti_sakit' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
            'alamat_selama_cuti' => 'required|string|max:255',
            'nomor_darurat' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);
        
        // 2. Hitung Total Hari Kerja (Senin-Jumat)
        $totalHari = 0;
        $curr = $startDate->copy();
        
        while ($curr->lte($endDate)) {
            // isWeekend() mengecek Sabtu & Minggu
            if (!$curr->isWeekend()) {
                $totalHari++;
            }
            $curr->addDay();
        }

        // 3. Validasi H-3 (Khusus Cuti Tahunan)
        // Syarat: Pengajuan minimal H-3 sebelum tanggal mulai
        if ($request->jenis_cuti == 'tahunan') {
            $today = Carbon::now()->startOfDay();
            $minDate = $today->copy()->addDays(3);

            if ($startDate->lt($minDate)) {
                return back()->withErrors(['tanggal_mulai' => 'Untuk Cuti Tahunan, pengajuan minimal H-3 (Mulai: ' . $minDate->format('d M Y') . ')'])->withInput();
            }
        }

        // 4. VALIDASI BARU: MASA KERJA < 1 TAHUN (Khusus Cuti Tahunan)
        // Jika user belum 1 tahun bekerja, tolak cuti TAHUNAN (Sakit boleh)
        if ($request->jenis_cuti == 'tahunan') {
            // Hitung selisih tahun dari created_at sampai sekarang
            if ($user->created_at->diffInYears(Carbon::now()) < 1) {
                return back()->withErrors(['jenis_cuti' => 'Maaf, masa kerja Anda belum 1 tahun. Anda belum berhak mengajukan Cuti Tahunan.'])->withInput();
            }

            // Validasi Sisa Kuota
            if ($user->kuota_cuti < $totalHari) {
                return back()->withErrors(['jenis_cuti' => 'Sisa kuota cuti tahunan tidak mencukupi.'])->withInput();
            }
        }

        // 5. Validasi Overlap (Tidak boleh mengajukan di tanggal yang sama dengan cuti lain yang aktif)
        $isOverlapping = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved_leader', 'approved'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate, $endDate])
                  ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->where('tanggal_mulai', '<=', $startDate)
                          ->where('tanggal_selesai', '>=', $endDate);
                  });
            })->exists();

        if ($isOverlapping) {
             return back()->withErrors(['tanggal_mulai' => 'Anda sudah memiliki pengajuan cuti pada rentang tanggal tersebut.'])->withInput();
        }

        // 6. Upload Bukti Sakit (Jika Ada)
        $filePath = null;
        if ($request->hasFile('bukti_sakit')) {
            $filePath = $request->file('bukti_sakit')->store('surat_dokter', 'public');
        }
        
        // 7. --- LOGIKA PENENTUAN STATUS AWAL (PERBAIKAN INI SUDAH BENAR) ---
        $initialStatus = 'pending';
        $leaderApprovedAt = null;
        $catatanLeader = null;

        // Jika user adalah Ketua Divisi, set status langsung ke 'approved_leader'
        if ($user->role === 'ketua_divisi') {
            $initialStatus = 'approved_leader'; 
            $leaderApprovedAt = now();          
            $catatanLeader = 'Pengajuan langsung oleh Ketua Divisi.';
        }
        // --------------------------------------------------------

        // 8. Simpan Pengajuan
        LeaveRequest::create([
            'user_id' => $user->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_hari' => $totalHari,
            'alasan' => $request->alasan,
            'bukti_sakit' => $filePath,
            'status' => $initialStatus, // Menggunakan status yang sudah disesuaikan
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'nomor_darurat' => $request->nomor_darurat,
            
            // Kolom ini akan terisi jika user adalah Ketua Divisi
            'catatan_leader' => $catatanLeader, 
            'approved_leader_at' => $leaderApprovedAt,
        ]);

        // 9. Potong Kuota (Hanya jika Cuti Tahunan)
        // PENTING: Kuota dipotong HANYA saat pengajuan dibuat. Jika ditolak/dibatalkan, kuota dikembalikan.
        if ($request->jenis_cuti == 'tahunan') {
            $user->decrement('kuota_cuti', $totalHari);
        }

        return redirect()->route('dashboard')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        // Pastikan yang melihat adalah pemilik, atasan, atau HRD/Admin
        $user = Auth::user();
        if ($user->id !== $leaveRequest->user_id && 
            $user->role !== 'admin' && 
            $user->role !== 'hrd' && 
            ($user->role !== 'ketua_divisi' || $user->divisiKetua->id !== $leaveRequest->user->divisi_id)) { // Logic Ketua Divisi
            abort(403);
        }

        return view('leaves.show', ['leave' => $leaveRequest]); // Mengganti nama variabel agar konsisten dengan view
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        // Hanya pemilik yang bisa membatalkan & status harus pending
        if (Auth::id() !== $leaveRequest->user_id) abort(403);
        
        // Pengecekan status yang boleh dibatalkan: Hanya 'pending' dan 'approved_leader'
        if (!in_array($leaveRequest->status, ['pending', 'approved_leader'])) {
            return back()->with('error', 'Tidak bisa dibatalkan karena sudah diproses final (Approved/Rejected).');
        }

        $request->validate([
            'alasan_pembatalan' => 'required|string'
        ]);

        // Kembalikan kuota jika tahunan
        if ($leaveRequest->jenis_cuti == 'tahunan') {
            $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
        }

        // Hapus file jika ada
        if ($leaveRequest->bukti_sakit) {
            Storage::disk('public')->delete($leaveRequest->bukti_sakit);
        }

        $leaveRequest->update([
            'status' => 'cancelled',
            'alasan_pembatalan' => $request->alasan_pembatalan
        ]);

        return back()->with('success', 'Pengajuan berhasil dibatalkan. Kuota cuti telah dikembalikan.');
    }

    public function downloadPdf(LeaveRequest $leaveRequest)
    {
        // Cek hak akses (Pemilik, HRD, Ketua Divisi)
        if (Auth::id() !== $leaveRequest->user_id && !in_array(Auth::user()->role, ['hrd', 'ketua_divisi'])) {
            abort(403);
        }

        if ($leaveRequest->status !== 'approved') {
            return back()->with('error', 'Hanya cuti yang disetujui yang dapat diunduh.');
        }

        // PASTIKAN MENGGUNAKAN 'leaves.pdf' SESUAI TEMPLATE MODERN
        $leave = $leaveRequest; 
        
        // Nama HRD Manager diambil dari user HRD pertama yang ditemukan
        $hrdManager = \App\Models\User::where('role', 'hrd')->first();
        
        // PENTING: PANGGIL FILE resources/views/leaves/pdf.blade.php
        $pdf = Pdf::loadView('leaves.pdf', compact('leave', 'hrdManager'));
        return $pdf->download('Surat_Izin_Cuti_' . $leaveRequest->user->name . '.pdf');
    }

    // --- LEADER & HRD METHODS (Sama seperti sebelumnya) ---

    public function leaderIndex()
    {
        $leader = Auth::user();
        if ($leader->role !== 'ketua_divisi') abort(403);

        $managedDivisi = $leader->divisiKetua;

        // Ambil pending request dari member divisi ini (kecuali diri sendiri)
        $pendingRequests = LeaveRequest::where('status', 'pending')
            ->whereHas('user', function ($q) use ($managedDivisi, $leader) {
                $q->where('divisi_id', $managedDivisi->id ?? 0)
                  ->where('id', '!=', $leader->id);
            })
            ->with('user.divisi')
            ->orderBy('created_at', 'asc') // Urutkan yang paling lama dulu
            ->get();

        return view('leaves.leader_index', compact('pendingRequests'));
    }

    public function leaderAction(Request $request, LeaveRequest $leaveRequest)
    {
        if (Auth::user()->role !== 'ketua_divisi') abort(403);

        $action = $request->input('action');
        $catatan = $request->input('catatan');

        if ($action === 'approve') {
            $leaveRequest->update([
                'status' => 'approved_leader',
                'catatan_leader' => $catatan,
                'approved_leader_at' => now(), // Catat waktu persetujuan
            ]);
        } elseif ($action === 'reject') {
            $request->validate(['catatan' => 'required|min:5']);
            
            // Refund kuota
            if ($leaveRequest->jenis_cuti == 'tahunan') {
                $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
            }

            $leaveRequest->update([
                'status' => 'rejected',
                'catatan_penolakan' => 'Ditolak Ketua Divisi: ' . $catatan
            ]);
        }

        return redirect()->route('leader.leaves.index')->with('success', 'Pengajuan berhasil diproses.');
    }

    public function hrdIndex()
    {
        if (Auth::user()->role !== 'hrd') abort(403);

        // HRD melihat semua pengajuan yang sudah disetujui Leader (status: approved_leader)
        $pendingRequests = LeaveRequest::with(['user', 'user.divisi'])
            ->where('status', 'approved_leader')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('leaves.hrd_index', compact('pendingRequests'));
    }

    public function hrdAction(Request $request, LeaveRequest $leaveRequest)
    {
        if (Auth::user()->role !== 'hrd') abort(403);

        $action = $request->input('action');
        $catatan = $request->input('catatan');

        if ($action === 'approve') {
             // Pastikan cuti sakit tidak memotong kuota tahunan
            $leaveRequest->update([
                'status' => 'approved',
                'approved_hrd_at' => now(),
                'catatan_hrd' => $catatan // Menambahkan kolom catatan HRD
            ]);
            $msg = 'Pengajuan disetujui (Final).';
        } elseif ($action === 'reject') {
            $request->validate(['catatan' => 'required|min:10']);

            // Refund kuota hanya jika cuti tahunan dan status sebelumnya bukan 'rejected'
            if ($leaveRequest->jenis_cuti == 'tahunan' && $leaveRequest->status !== 'rejected') {
                $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
            }

            $leaveRequest->update([
                'status' => 'rejected',
                'catatan_penolakan' => 'Ditolak HRD: ' . $catatan,
                'approved_hrd_at' => now(),
            ]);
            $msg = 'Pengajuan ditolak.';
        }

        return redirect()->route('hrd.leaves.index')->with('success', $msg ?? 'Diproses.');
    }

    // FUNGSI hrdBulkAction TELAH DIHAPUS

    public function report()
    {
        if (Auth::user()->role !== 'hrd') abort(403);
        $leaves = LeaveRequest::with('user.divisi')->orderBy('created_at', 'desc')->get();
        return view('leaves.report', compact('leaves'));
    }
}