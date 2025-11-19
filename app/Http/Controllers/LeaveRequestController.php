<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Carbon\Carbon; // Library untuk urus tanggal
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    // 1. Tampilkan Halaman Form Pengajuan
    public function create()
    {
        return view('leaves.create');
    }

    // 2. Proses Simpan Pengajuan
    public function store(Request $request)
    {
        // A. Validasi Input Dasar
        $request->validate([
            'jenis_cuti' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required',
            'bukti_sakit' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ]);

        $user = Auth::user();
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);

        // B. Validasi Aturan H-3 untuk Cuti Tahunan [cite: 36]
        if ($request->jenis_cuti == 'tahunan') {
            $minDate = Carbon::today()->addDays(3);
            if ($startDate->lt($minDate)) {
                return back()->withErrors(['tanggal_mulai' => 'Cuti tahunan wajib diajukan minimal H-3.']);
            }
        }

        // C. Hitung Total Hari Kerja (Senin-Jumat) [cite: 121]
        $totalHari = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            if (!$currentDate->isWeekend()) { // Jika bukan Sabtu/Minggu
                $totalHari++;
            }
            $currentDate->addDay();
        }

        if ($totalHari == 0) {
            return back()->withErrors(['tanggal_mulai' => 'Anda memilih hari libur (Sabtu/Minggu). Pilih hari kerja.']);
        }

        // D. Cek Sisa Kuota (Khusus Cuti Tahunan)
        if ($request->jenis_cuti == 'tahunan') {
            if ($user->kuota_cuti < $totalHari) {
                return back()->withErrors(['jenis_cuti' => 'Sisa kuota cuti Anda tidak mencukupi.']);
            }
        }

        // E. Upload File (Jika Cuti Sakit) 
        $filePath = null;
        if ($request->jenis_cuti == 'sakit') {
            if (!$request->hasFile('bukti_sakit')) {
                return back()->withErrors(['bukti_sakit' => 'Wajib upload surat dokter untuk cuti sakit.']);
            }
            $filePath = $request->file('bukti_sakit')->store('surat_dokter', 'public');
        }

        // F. Simpan ke Database
        LeaveRequest::create([
            'user_id' => $user->id,
            'jenis_cuti' => $request->jenis_cuti,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'total_hari' => $totalHari,
            'alasan' => $request->alasan,
            'bukti_sakit' => $filePath,
            'status' => 'pending', // Status awal [cite: 27]
        ]);

        if ($request->jenis_cuti == 'tahunan') {
            $user->decrement('kuota_cuti', $totalHari);
        }

        return redirect()->route('dashboard')->with('success', 'Pengajuan cuti berhasil dikirim!');
    }

    public function leaderIndex()
    {
        $leaderId = Auth::id();
        
        // 1. Cari semua divisi yang dipimpin oleh user yang sedang login
        $managedDivisiIds = Divisi::where('ketua_divisi_id', $leaderId)->pluck('id');
        
        // 2. Ambil semua pengajuan cuti dari anggota divisi yang dipimpinnya,
        // yang statusnya masih 'pending'
        $pendingRequests = LeaveRequest::where('status', 'pending')
            ->whereHas('user', function ($query) use ($managedDivisiIds, $leaderId) {
                // Pengajuan hanya dari bawahan (bukan dari diri sendiri)
                $query->where('id', '!=', $leaderId) 
                    ->whereIn('divisi_id', $managedDivisiIds); 
            })
            ->with('user.divisi')
            ->get();

        return view('leaves.leader_index', compact('pendingRequests'));
    }

    // Aksi Approve/Reject oleh Ketua Divisi
    public function leaderAction(Request $request, LeaveRequest $leaveRequest)
    {
        $action = $request->input('action'); // 'approve' atau 'reject'
        $catatan = $request->input('catatan');

        // Autorisasi Cek: Hanya Ketua Divisi yang bisa akses
        if (Auth::user()->role !== 'ketua_divisi') {
            return back()->with('error', 'Anda tidak berhak melakukan aksi ini.');
        }

        if ($action === 'approve') {
            $leaveRequest->update([
                'status' => 'approved_leader', // Status diteruskan ke HRD [cite: 27]
                'catatan_leader' => $catatan, // Catatan opsional
            ]);
            $message = 'Pengajuan cuti berhasil disetujui dan diteruskan ke HRD.';

        } elseif ($action === 'reject') {
            $request->validate(['catatan' => 'required|min:10']); 

            if ($leaveRequest->jenis_cuti == 'tahunan') {
                $leaveRequest->user->increment('kuota_cuti', $leaveRequest->total_hari);
            }

            $leaveRequest->update([
                'status' => 'rejected',
                'catatan_penolakan' => $catatan,
            ]);
            $message = 'Pengajuan cuti berhasil ditolak.';

        } else {
            return back()->with('error', 'Aksi tidak valid.');
        }

        // Arahkan kembali ke halaman verifikasi
        return redirect()->route('leader.leaves.index')->with('success', $message);
    }
}