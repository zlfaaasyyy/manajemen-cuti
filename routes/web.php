<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;
use App\Models\LeaveRequest; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login'); // Arahkan langsung ke halaman login
});

// --- DASHBOARD ROUTE ---
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Ambil riwayat cuti milik user yang sedang login (untuk tabel riwayat)
    $riwayatCuti = LeaveRequest::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Hitung statistik sederhana untuk tampilan Dashboard Karyawan
    $totalCuti = $riwayatCuti->where('status', 'approved')->sum('total_hari');
    $statusPending = $riwayatCuti->whereIn('status', ['pending', 'approved_leader'])->count();

    return view('dashboard', compact('riwayatCuti', 'totalCuti', 'statusPending'));
})->middleware(['auth', 'verified'])->name('dashboard');


// --- AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function () {
    
    // 1. Profile Routes (Breeze Default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Manajemen Data Master (Admin)
    Route::resource('divisi', DivisiController::class);
    Route::resource('users', UserController::class);

    // 3. Fitur Pengajuan Cuti (Karyawan & Ketua Divisi)
    Route::get('/ajukan-cuti', [LeaveRequestController::class, 'create'])->name('leaves.create');
    Route::post('/ajukan-cuti', [LeaveRequestController::class, 'store'])->name('leaves.store');
    
    // Action: Batalkan Cuti (Hanya status Pending)
    Route::delete('/cuti/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leaves.cancel');
    
    // Action: Download PDF Surat Cuti (Hanya status Approved)
    Route::get('/cuti/{leaveRequest}/pdf', [LeaveRequestController::class, 'downloadPdf'])->name('leaves.pdf');

    // 4. Fitur Verifikasi Leader (Ketua Divisi)
    Route::get('/verifikasi-leader', [LeaveRequestController::class, 'leaderIndex'])->name('leader.leaves.index');
    Route::post('/verifikasi-leader/{leaveRequest}/action', [LeaveRequestController::class, 'leaderAction'])->name('leader.leaves.action');

    // 5. Fitur Verifikasi HRD
    Route::get('/hrd/verifikasi', [LeaveRequestController::class, 'hrdIndex'])->name('hrd.leaves.index');
    Route::post('/hrd/verifikasi/{leaveRequest}', [LeaveRequestController::class, 'hrdAction'])->name('hrd.leaves.action');

    // 6. Laporan Cuti (Admin & HRD)
    Route::get('/laporan-cuti', [LeaveRequestController::class, 'report'])->name('leaves.report');
});

require __DIR__.'/auth.php';