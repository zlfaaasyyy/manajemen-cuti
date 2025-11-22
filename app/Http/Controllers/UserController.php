<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('divisi')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $divisis = Divisi::all();
        
        // 1. Base Role (Tanpa Admin)
        $roles = ['ketua_divisi', 'user'];

        // 2. Cek apakah posisi HRD sudah terisi?
        // Jika BELUM ada user dengan role 'hrd', masukkan opsi 'hrd' ke dropdown
        if (!User::where('role', 'hrd')->exists()) {
            array_unshift($roles, 'hrd'); // Masukkan ke urutan awal
        }
        
        return view('users.create', compact('divisis', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'hrd', 'ketua_divisi', 'user'])],
            'divisi_id' => 'nullable|exists:divisis,id',
            'kuota_cuti' => 'nullable|integer|min:0',
        ]);

        $kuota = $request->kuota_cuti;
        if ($request->role === 'hrd' || $request->role === 'admin') {
            $kuota = 0;
        } elseif ($kuota === null) {
            $kuota = 12;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'divisi_id' => $request->divisi_id,
            'kuota_cuti' => $kuota,
        ]);

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $divisi = Divisi::all();
        
        // 1. Base Role (Tanpa Admin)
        $roles = ['ketua_divisi', 'user'];

        // 2. Logika HRD Cerdas untuk Edit:
        // Opsi HRD ditampilkan jika:
        // A. User yang sedang diedit INI memang HRD (agar role dia tidak hilang/berubah), ATAU
        // B. Belum ada HRD lain di sistem.
        
        // Cek apakah ada HRD SELAIN user yang sedang diedit ini
        $hrdLainExists = User::where('role', 'hrd')->where('id', '!=', $user->id)->exists();

        if (!$hrdLainExists) {
             array_unshift($roles, 'hrd');
        }

        return view('users.edit', compact('user', 'divisi', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], 
            'role' => ['required', Rule::in(['admin', 'hrd', 'ketua_divisi', 'user'])],
            'divisi_id' => 'nullable|exists:divisis,id',
            'kuota_cuti' => 'required|integer|min:0',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'role', 'divisi_id', 'kuota_cuti']);

        if ($request->role === 'hrd' || $request->role === 'admin') {
            $data['kuota_cuti'] = 0;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) return back()->with('error', 'Gagal hapus akun sendiri.');
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User dihapus.');
    }
}