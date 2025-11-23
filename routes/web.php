<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;
use App\Models\LeaveRequest; 
use App\Models\Divisi; // Tambahkan
use App\Models\User; // Tambahkan

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

// --- DASHBOARD ROUTE (LOGIKA STATISTIK DINAMIS) ---
Route::get('/dashboard', function () {
    $user = Auth::user();
    $today = \Carbon\Carbon::today();
    $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
    $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
    $startOfWeek = \Carbon\Carbon::now()->startOfWeek();
    $endOfWeek = \Carbon\Carbon::now()->endOfWeek();

    // DATA UMUM (Riwayat Cuti Pribadi)
    $riwayatCuti = LeaveRequest::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    $stats = [];

    // 1. LOGIKA ADMIN
    if ($user->role === 'admin') {
        $stats['total_karyawan'] = User::count();
        $stats['total_divisi'] = Divisi::count();
        $stats['pengajuan_bulan_ini'] = LeaveRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        // Masa kerja < 1 tahun (Asumsi berdasarkan created_at)
        $stats['karyawan_baru'] = User::where('created_at', '>=', \Carbon\Carbon::now()->subYear())->count();
    }

    // 2. LOGIKA KARYAWAN (USER)
    if ($user->role === 'user') {
        $stats['sisa_kuota'] = $user->kuota_cuti;
        $stats['cuti_sakit_diajukan'] = LeaveRequest::where('user_id', $user->id)->where('jenis_cuti', 'sakit')->count();
        $stats['total_pengajuan'] = LeaveRequest::where('user_id', $user->id)->count();
        $stats['nama_divisi'] = $user->divisi->nama ?? '-';
        $stats['nama_ketua'] = $user->divisi->ketuaDivisi->name ?? '-';
    }

    // 3. LOGIKA KETUA DIVISI
    if ($user->role === 'ketua_divisi') {
        $divisiId = $user->divisiKetua->id ?? null;
        
        $stats['total_masuk'] = LeaveRequest::whereHas('user', function($q) use ($divisiId) {
            $q->where('divisi_id', $divisiId);
        })->count();

        $stats['pending_verifikasi'] = LeaveRequest::where('status', 'pending')
            ->whereHas('user', function($q) use ($divisiId, $user) {
                $q->where('divisi_id', $divisiId)->where('id', '!=', $user->id);
            })->count();
        
        // Anggota Cuti Minggu Ini
        $stats['sedang_cuti'] = LeaveRequest::where('status', 'approved')
            ->whereHas('user', function($q) use ($divisiId) { $q->where('divisi_id', $divisiId); })
            ->where(function($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('tanggal_mulai', [$startOfWeek, $endOfWeek])
                  ->orWhereBetween('tanggal_selesai', [$startOfWeek, $endOfWeek]);
            })->count();
    }

    // 4. LOGIKA HRD
    if ($user->role === 'hrd') {
        $stats['total_bulan_ini'] = LeaveRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        
        // Pending Final Approval (Approved by Leader OR Pending from Ketua Divisi)
        $stats['pending_final'] = LeaveRequest::where('status', 'approved_leader')
            ->orWhere(function($q) {
                $q->where('status', 'pending')->whereHas('user', fn($u) => $u->where('role', 'ketua_divisi'));
            })->count();

        // Karyawan Sedang Cuti Bulan Ini
        $stats['sedang_cuti_bulan_ini'] = LeaveRequest::where('status', 'approved')
            ->where(function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth]);
            })->count();
            
        $stats['total_divisi'] = Divisi::count();
    }

    return view('dashboard', compact('riwayatCuti', 'stats'));
})->middleware(['auth', 'verified'])->name('dashboard');


// --- AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function () {
    
    // 1. Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2. Manajemen Data Master (Admin)
    Route::resource('divisi', DivisiController::class);
    Route::resource('users', UserController::class);

    // 3. Fitur Pengajuan Cuti (Karyawan & Ketua Divisi)
    Route::get('/ajukan-cuti', [LeaveRequestController::class, 'create'])->name('leaves.create');
    Route::post('/ajukan-cuti', [LeaveRequestController::class, 'store'])->name('leaves.store');
    
    Route::delete('/cuti/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leaves.cancel');
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