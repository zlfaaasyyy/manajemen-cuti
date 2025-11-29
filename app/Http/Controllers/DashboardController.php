<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\Divisi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController; 
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        $stats = [];
        $extraData = []; 
        $riwayatCuti = collect(); 

        if ($user->role === 'user' || $user->role === 'ketua_divisi') {
            $riwayatCuti = LeaveRequest::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        if ($user->role === 'admin') {
            $totalUser = User::count();
            
            $sedangCuti = User::whereHas('leaveRequests', function(Builder $query) use ($now) {
                $query->where('status', 'approved')
                      ->whereDate('tanggal_mulai', '<=', $now)
                      ->whereDate('tanggal_selesai', '>=', $now);
            })->count();

            $userAktif = $totalUser - $sedangCuti;

            $stats['total_karyawan'] = $totalUser;
            $stats['karyawan_aktif'] = $userAktif; 
            $stats['karyawan_cuti'] = $sedangCuti; 
            $stats['total_divisi'] = Divisi::count();
            $stats['pengajuan_bulan_ini'] = LeaveRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $stats['pending_approval'] = LeaveRequest::whereIn('status', ['pending', 'approved_leader'])->count();
            
            $extraData['karyawan_baru'] = User::whereIn('role', ['user', 'ketua_divisi', 'hrd']) 
                ->where('created_at', '>', $now->copy()->subYear())
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($user->role === 'user') {
            $stats['sisa_kuota'] = $user->kuota_cuti;
            $stats['cuti_sakit'] = LeaveRequest::where('user_id', $user->id)->where('jenis_cuti', 'sakit')->count();
            $stats['total_pengajuan'] = LeaveRequest::where('user_id', $user->id)->count();
            
            $extraData['divisi'] = $user->divisi;
            $extraData['ketua'] = $user->divisi?->ketuaDivisi;
        }

        if ($user->role === 'ketua_divisi') {
            $divisiId = $user->divisiKetua->id ?? null;
            
            $stats['total_masuk'] = LeaveRequest::whereHas('user', function($q) use ($divisiId) {
                $q->where('divisi_id', $divisiId);
            })->count();

            $stats['pending_verifikasi'] = LeaveRequest::where('status', 'pending')
                ->whereHas('user', function($q) use ($divisiId, $user) {
                    $q->where('divisi_id', $divisiId)->where('id', '!=', $user->id);
                })->count();
            
            $extraData['anggota_divisi'] = User::where('divisi_id', $divisiId)->get();

            $extraData['sedang_cuti_minggu_ini'] = User::where('divisi_id', $divisiId)
                ->whereHas('leaveRequests', function($q) use ($startOfWeek, $endOfWeek) {
                    $q->where('status', 'approved')
                      ->where(function($dateQ) use ($startOfWeek, $endOfWeek) {
                          $dateQ->whereBetween('tanggal_mulai', [$startOfWeek, $endOfWeek])
                                ->orWhereBetween('tanggal_selesai', [$startOfWeek, $endOfWeek]);
                      });
                })->with(['leaveRequests' => function($q) use ($startOfWeek, $endOfWeek) {
                     $q->where('status', 'approved')->where('tanggal_selesai', '>=', $startOfWeek);
                 }])->get();
        }

        if ($user->role === 'hrd') {
            $stats['total_bulan_ini'] = LeaveRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            
            $stats['pending_final'] = LeaveRequest::where('status', 'approved_leader')
                ->orWhere(function($q) {
                    $q->where('status', 'pending')->whereHas('user', fn($u) => $u->where('role', 'ketua_divisi'));
                })->count();

            $stats['total_divisi'] = Divisi::count();

            $extraData['cuti_bulan_ini'] = User::whereHas('leaveRequests', function(Builder $q) use ($startOfMonth, $endOfMonth) {
                $q->where('status', 'approved')
                  ->where(function ($dateQ) use ($startOfMonth, $endOfMonth) {
                      $dateQ->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                            ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth]); 
                  });
            })->with('divisi')->get();

            $extraData['daftar_divisi'] = Divisi::withCount('users')->with('ketuaDivisi')->get();
        }

        return view('dashboard', compact('riwayatCuti', 'stats', 'extraData'));
    }
}