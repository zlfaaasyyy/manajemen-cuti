<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // 1. Tampilkan Daftar User
    public function index()
    {
        // Ambil semua user kecuali admin (opsional, agar admin tidak hapus diri sendiri)
        $users = User::with('divisi')->where('role', '!=', 'admin')->get();
        return view('users.index', compact('users'));
    }

    // 2. Form Tambah User
    public function create()
    {
        $divisis = Divisi::all();
        return view('users.create', compact('divisis'));
    }

    // 3. Simpan User Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required',
            'divisi_id' => 'nullable|exists:divisis,id',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'divisi_id' => $request->divisi_id,
            'kuota_cuti' => 12, // Default 12 hari
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    // 4. Form Edit User
    public function edit(User $user)
    {
        $divisis = Divisi::all();
        return view('users.edit', compact('user', 'divisis'));
    }

    // 5. Update User
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'username' => ['required', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required',
        ]);

        // Update data (password hanya diupdate jika diisi)
        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate!');
    }

    // 6. Hapus User
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User dihapus!');
    }
}