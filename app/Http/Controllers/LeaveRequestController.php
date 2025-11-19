<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
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

        // G. Kurangi Kuota User (Langsung potong saat pending, nanti balik kalau reject) [cite: 37]
        if ($request->jenis_cuti == 'tahunan') {
            $user->decrement('kuota_cuti', $totalHari);
        }

        return redirect()->route('dashboard')->with('success', 'Pengajuan cuti berhasil dikirim!');
    }
}