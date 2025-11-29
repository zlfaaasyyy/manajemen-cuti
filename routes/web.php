<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\DashboardController; // Tambahkan import ini

use Illuminate\Support\Facades\Route;

// Hapus import Model yang tidak perlu lagi karena logic pindah ke Controller
// use App\Models\LeaveRequest; 
// use App\Models\Divisi;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Carbon\Carbon;


Route::get('/', function () {
    // Arahkan langsung ke halaman login saat akses root
    return view('auth.login');
});

// --- AUTHENTICATED ROUTES (Memerlukan Login) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Rute dashboard utama - DITUNJUKKAN KE CONTROLLER
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Manajemen Divisi & User (Admin)
    Route::resource('divisi', DivisiController::class);
    Route::post('/divisi/{divisi}/add-member', [DivisiController::class, 'addMember'])->name('divisi.addMember');
    Route::delete('/divisi/remove-member/{user}', [DivisiController::class, 'removeMember'])->name('divisi.removeMember');
    Route::resource('users', UserController::class);

    // Modul Cuti Karyawan (User & Ketua Divisi)
    Route::get('/riwayat-cuti', [LeaveRequestController::class, 'index'])->name('leaves.index');
    Route::get('/detail-cuti/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leaves.show');
    Route::get('/ajukan-cuti', [LeaveRequestController::class, 'create'])->name('leaves.create');
    Route::post('/ajukan-cuti', [LeaveRequestController::class, 'store'])->name('leaves.store');
    Route::delete('/cuti/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leaves.cancel');
    Route::get('/cuti/{leaveRequest}/pdf', [LeaveRequestController::class, 'downloadPdf'])->name('leaves.pdf');

    // Modul Leader (Ketua Divisi)
    Route::get('/verifikasi-leader', [LeaveRequestController::class, 'leaderIndex'])->name('leader.leaves.index');
    Route::post('/verifikasi-leader/{leaveRequest}/action', [LeaveRequestController::class, 'leaderAction'])->name('leader.leaves.action');

    // Modul HRD
    Route::get('/hrd/verifikasi', [LeaveRequestController::class, 'hrdIndex'])->name('hrd.leaves.index');
    Route::post('/hrd/verifikasi/{leaveRequest}', [LeaveRequestController::class, 'hrdAction'])->name('hrd.leaves.action');
    Route::post('/hrd/verifikasi/bulk', [LeaveRequestController::class, 'hrdBulkAction'])->name('hrd.leaves.bulk_action');
    
    Route::get('/laporan-cuti', [LeaveRequestController::class, 'report'])->name('leaves.report');
});

require __DIR__.'/auth.php';