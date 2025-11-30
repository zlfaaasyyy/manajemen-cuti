<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        // Ambil User yang SEDANG CUTI
        $usersOnLeave = User::whereIn('role', ['user', 'ketua_divisi'])
            ->whereHas('leaveRequests', function($q) use ($now) {
                $q->where('status', 'approved')
                  ->whereDate('tanggal_mulai', '<=', $now)
                  ->whereDate('tanggal_selesai', '>=', $now);
            })->with(['divisi', 'leaveRequests' => function($q) use ($now) {
                $q->where('status', 'approved')
                  ->whereDate('tanggal_mulai', '<=', $now)
                  ->whereDate('tanggal_selesai', '>=', $now);
            }])->get();

        $onLeaveIds = $usersOnLeave->pluck('id')->toArray();

        // Ambil User AKTIF (Sisanya)
        $usersQuery = User::whereNotIn('id', $onLeaveIds)->with('divisi');

        // =========================================================
        // START: FITUR FILTER & SORTIR BARU
        // =========================================================

        // 1. FILTER BERDASARKAN ROLE
        if ($request->filled('role') && $request->role != 'all') {
            $usersQuery->where('role', $request->role);
        }
        
        // 2. FILTER BERDASARKAN DIVISI
        if ($request->filled('divisi_id') && $request->divisi_id != 'all') {
            $usersQuery->where('divisi_id', $request->divisi_id);
        }
        
        // 3. SORTIR
        $sortBy = $request->get('sort_by', 'name'); // Default sort: name
        $sortOrder = $request->get('sort_order', 'asc'); // Default order: asc

        // Validasi Sortir
        if (in_array($sortBy, ['name', 'created_at', 'kuota_cuti'])) {
            $usersQuery->orderBy($sortBy, $sortOrder);
        } 
        // Tambahan logika khusus untuk sorting berdasarkan nama divisi
        elseif ($sortBy == 'divisi_id') {
            $usersQuery->leftJoin('divisis', 'users.divisi_id', '=', 'divisis.id')
                       ->orderBy('divisis.nama', $sortOrder)
                       ->select('users.*'); // Pilih kembali kolom users
        } else {
             $usersQuery->orderBy('name', 'asc'); // Fallback
        }
        // =========================================================
        // END: FITUR FILTER & SORTIR BARU
        // =========================================================


        $users = $usersQuery->paginate(10)->withQueryString(); 
        
        // Data tambahan untuk View (Diperlukan untuk Filter Options di index)
        $divisis = Divisi::all();
        $roles = ['admin', 'hrd', 'ketua_divisi', 'user']; // Semua role yang mungkin
        

        return view('users.index', compact('users', 'usersOnLeave', 'divisis', 'roles'));
    }

    public function create()
    {
        $divisis = Divisi::all();
        
        // LOGIKA FILTER ROLE:
        $roles = ['ketua_divisi', 'user']; 

        // Cek apakah slot HRD masih kosong?
        if (!User::where('role', 'hrd')->exists()) {
            array_unshift($roles, 'hrd'); 
        }

        // Cek apakah slot Admin masih kosong?
        if (!User::where('role', 'admin')->exists()) {
            array_unshift($roles, 'admin'); 
        }

        return view('users.create', compact('divisis', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users', // <--- [MODIFIKASI/BARU] Validasi Username
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'hrd', 'ketua_divisi', 'user'])],
            'divisi_id' => [
                'nullable', 
                'exists:divisis,id', 
                Rule::when($request->role === 'ketua_divisi', [
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $divisi = Divisi::find($value);
                            if ($divisi->ketua_divisi_id !== null) {
                                $fail('Divisi ini sudah memiliki Ketua.');
                            }
                        }
                    }
                ])
            ],
            'kuota_cuti' => [
                Rule::requiredIf(in_array($request->role, ['user', 'ketua_divisi'])), 
                'nullable', 'integer', 'min:0'
            ],
        ]);

        $kuota = in_array($request->role, ['admin', 'hrd']) ? 0 : ($request->kuota_cuti ?? 12);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username, // <--- [MODIFIKASI/BARU] Simpan Username
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'divisi_id' => $request->divisi_id,
            'kuota_cuti' => $kuota,
        ]);

        if ($request->role === 'ketua_divisi' && $request->divisi_id) {
            Divisi::where('id', $request->divisi_id)->update(['ketua_divisi_id' => $user->id]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $divisis = Divisi::all(); 
        $roles = ['ketua_divisi', 'user'];

        // Cek slot HRD (Kecuali user ini sendiri adalah HRD)
        $hrdExists = User::where('role', 'hrd')->where('id', '!=', $user->id)->exists();
        if (!$hrdExists) {
             array_unshift($roles, 'hrd');
        }

        // Cek slot Admin (Kecuali user ini sendiri adalah Admin)
        $adminExists = User::where('role', 'admin')->where('id', '!=', $user->id)->exists();
        if (!$adminExists) {
            array_unshift($roles, 'admin');
        }

        return view('users.edit', compact('user', 'divisis', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $currentManagedDivisiId = $user->divisiKetua->id ?? null;

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)], // <--- [MODIFIKASI/BARU] Validasi Username
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], 
            'role' => ['required', Rule::in(['admin', 'hrd', 'ketua_divisi', 'user'])],
            'divisi_id' => [
                'nullable', 
                'exists:divisis,id',
                Rule::when($request->role === 'ketua_divisi', [
                    function ($attribute, $value, $fail) use ($user) {
                        if ($value) {
                            $divisi = Divisi::find($value);
                            // Cek jika divisi sudah punya ketua DAN ketuanya bukan user ini
                            if ($divisi->ketua_divisi_id !== null && $divisi->ketua_divisi_id !== $user->id) {
                                $fail('Divisi ini sudah memiliki Ketua.');
                            }
                        }
                    }
                ])
            ],
            'kuota_cuti' => ['nullable', 'integer', 'min:0'],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'username', 'email', 'role', 'divisi_id']); // <--- [MODIFIKASI] Masukkan username ke data

        // Force Kuota 0 untuk Admin/HRD
        if (in_array($request->role, ['admin', 'hrd'])) {
            $data['kuota_cuti'] = 0;
        } else {
            $data['kuota_cuti'] = $request->kuota_cuti ?? 12;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        // Jika user sebelumnya Ketua Divisi, dan sekarang ganti Divisi atau ganti Role
        if ($user->role === 'ketua_divisi' && $currentManagedDivisiId && $currentManagedDivisiId != $request->divisi_id) {
            Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }
        
        $user->update($data);

        // Update Divisi baru
        if ($user->role === 'ketua_divisi' && $request->divisi_id) {
            Divisi::where('id', $request->divisi_id)->update(['ketua_divisi_id' => $user->id]);
        }
        
        // Jika role berubah jadi bukan ketua, hapus kepemilikan
        if ($user->role !== 'ketua_divisi') {
             Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Rule 1: Cannot delete self
        if (auth()->id() === $user->id) {
             return back()->with('error', 'Gagal hapus akun sendiri.');
        }

        // Rule 2: Cannot delete HRD (karena harus selalu ada dan hanya ada 1 slot)
        if ($user->role === 'hrd') {
            return back()->with('error', 'Akun HRD tidak dapat dihapus karena perannya vital dan harus selalu ada.');
        }
        
        // Rule 3: Cannot delete the only Admin
        if ($user->role === 'admin') {
            $otherAdmins = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
            if ($otherAdmins === 0) {
                 return back()->with('error', 'Akun Admin tidak dapat dihapus karena tidak ada Admin lain yang tersisa.');
            }
        }

        // Reset Ketua Divisi relationship if applicable
        if ($user->role === 'ketua_divisi') {
            Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}