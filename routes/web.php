<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;
use App\Models\LeaveRequest; 
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

Route::get('/', function () {
    return view('auth.login');
});

// --- DASHBOARD ROUTE (LOGIC PUSAT) ---
Route::get('/dashboard', function () {
    $user = Auth::user();
    $now = Carbon::now();
    $startOfMonth = $now->copy()->startOfMonth();
    $endOfMonth = $now->copy()->endOfMonth();
    $startOfWeek = $now->copy()->startOfWeek();
    $endOfWeek = $now->copy()->endOfWeek();

    // Data Global (Riwayat User Sendiri - Limit 5)
    $riwayatCuti = LeaveRequest::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $stats = [];
    $extraData = []; // Untuk menampung List/Tabel tambahan

    // 1. LOGIC ADMIN
    if ($user->role === 'admin') {
        // Total Karyawan (Asumsi semua aktif karena belum ada fitur soft delete/status)
        $stats['total_karyawan'] = User::count(); 
        $stats['total_divisi'] = Divisi::count();
        $stats['pengajuan_bulan_ini'] = LeaveRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        // Pending Approval (Global)
        $stats['pending_approval'] = LeaveRequest::whereIn('status', ['pending', 'approved_leader'])->count();
        
        // Daftar karyawan masa kerja < 1 tahun (Belum eligible cuti tahunan)
        // Logic: Created_at > 1 tahun yang lalu
        $extraData['karyawan_baru'] = User::where('role', 'user')
            ->where('created_at', '>', $now->copy()->subYear())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // 2. LOGIC USER (KARYAWAN)
    if ($user->role === 'user') {
        $stats['sisa_kuota'] = $user->kuota_cuti;
        $stats['cuti_sakit'] = LeaveRequest::where('user_id', $user->id)->where('jenis_cuti', 'sakit')->count();
        $stats['total_pengajuan'] = LeaveRequest::where('user_id', $user->id)->count();
        
        // Info Divisi & Ketua
        $extraData['divisi'] = $user->divisi;
        $extraData['ketua'] = $user->divisi?->ketuaDivisi;
    }

    // 3. LOGIC KETUA DIVISI
    if ($user->role === 'ketua_divisi') {
        $divisiId = $user->divisiKetua->id ?? null;
        
        // Total Masuk (Dari anak buah)
        $stats['total_masuk'] = LeaveRequest::whereHas('user', function($q) use ($divisiId) {
            $q->where('divisi_id', $divisiId);
        })->count();

        // Pending Verifikasi (Menunggu aksi saya)
        $stats['pending_verifikasi'] = LeaveRequest::where('status', 'pending')
            ->whereHas('user', function($q) use ($divisiId, $user) {
                $q->where('divisi_id', $divisiId)->where('id', '!=', $user->id);
            })->count();
        
        // Anggota Divisi
        $extraData['anggota_divisi'] = User::where('divisi_id', $divisiId)->get();

        // Sedang Cuti Minggu Ini (Approved & Overlap tanggal)
        $extraData['sedang_cuti_minggu_ini'] = User::where('divisi_id', $divisiId)
            ->whereHas('leaveRequests', function($q) use ($startOfWeek, $endOfWeek) {
                $q->where('status', 'approved')
                  ->where(function($dateQ) use ($startOfWeek, $endOfWeek) {
                      $dateQ->whereBetween('tanggal_mulai', [$startOfWeek, $endOfWeek])
                            ->orWhereBetween('tanggal_selesai', [$startOfWeek, $endOfWeek])
                            ->orWhere(function($sub) use ($startOfWeek, $endOfWeek) {
                                $sub->where('tanggal_mulai', '<=', $startOfWeek)
                                    ->where('tanggal_selesai', '>=', $endOfWeek);
                            });
                  });
            })->with(['leaveRequests' => function($q) use ($startOfWeek, $endOfWeek) {
                // Eager load hanya request yg relevan untuk ditampilkan tanggalnya
                 $q->where('status', 'approved')->where('tanggal_selesai', '>=', $startOfWeek);
            }])->get();
    }

    // 4. LOGIC HRD
    if ($user->role === 'hrd') {
        $stats['total_bulan_ini'] = LeaveRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        
        // Pending Final (Approved Leader OR Pending from Ketua Divisi)
        $stats['pending_final'] = LeaveRequest::where('status', 'approved_leader')
            ->orWhere(function($q) {
                $q->where('status', 'pending')->whereHas('user', fn($u) => $u->where('role', 'ketua_divisi'));
            })->count();

        $stats['total_divisi'] = Divisi::count();

        // Karyawan Sedang Cuti BULAN INI
        $extraData['cuti_bulan_ini'] = User::whereHas('leaveRequests', function($q) use ($startOfMonth, $endOfMonth) {
                $q->where('status', 'approved')
                  ->where(function($dateQ) use ($startOfMonth, $endOfMonth) {
                      $dateQ->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                            ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth]);
                  });
            })->with('divisi')->get();

        // Daftar Semua Divisi
        $extraData['daftar_divisi'] = Divisi::withCount('users')->with('ketuaDivisi')->get();
    }

    return view('dashboard', compact('riwayatCuti', 'stats', 'extraData'));
})->middleware(['auth', 'verified'])->name('dashboard');


// --- AUTHENTICATED ROUTES ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('divisi', DivisiController::class);
    Route::post('/divisi/{divisi}/add-member', [DivisiController::class, 'addMember'])->name('divisi.addMember');
    Route::delete('/divisi/remove-member/{user}', [DivisiController::class, 'removeMember'])->name('divisi.removeMember');
    Route::resource('users', UserController::class);

    Route::get('/riwayat-cuti', [LeaveRequestController::class, 'index'])->name('leaves.index');
    Route::get('/detail-cuti/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leaves.show');
    Route::get('/ajukan-cuti', [LeaveRequestController::class, 'create'])->name('leaves.create');
    Route::post('/ajukan-cuti', [LeaveRequestController::class, 'store'])->name('leaves.store');
    Route::delete('/cuti/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leaves.cancel');
    Route::get('/cuti/{leaveRequest}/pdf', [LeaveRequestController::class, 'downloadPdf'])->name('leaves.pdf');

    Route::get('/verifikasi-leader', [LeaveRequestController::class, 'leaderIndex'])->name('leader.leaves.index');
    Route::post('/verifikasi-leader/{leaveRequest}/action', [LeaveRequestController::class, 'leaderAction'])->name('leader.leaves.action');

    Route::get('/hrd/verifikasi', [LeaveRequestController::class, 'hrdIndex'])->name('hrd.leaves.index');
    Route::post('/hrd/verifikasi/{leaveRequest}', [LeaveRequestController::class, 'hrdAction'])->name('hrd.leaves.action');
    // HAPUS RUTE BULK ACTION
    // Route::post('/hrd/verifikasi/bulk', [LeaveRequestController::class, 'hrdBulkAction'])->name('hrd.leaves.bulk_action');
    Route::get('/laporan-cuti', [LeaveRequestController::class, 'report'])->name('leaves.report');
});

require __DIR__.'/auth.php';