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
            'divisi_id' => [
                'nullable', 
                'exists:divisis,id',
                // VALIDASI BARU: Jika user adalah Ketua Divisi, pastikan Divisi tsb belum punya Ketua lain
                Rule::when($request->role === 'ketua_divisi', [
                    // Cek apakah Divisi yang dipilih (jika ada) sudah punya ketuaDivisi_id yang terisi
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $divisi = Divisi::find($value);
                            // Cek jika Divisi sudah punya ketua, maka ini tidak boleh
                            if ($divisi->ketua_divisi_id !== null) {
                                $fail('Divisi ini sudah memiliki Ketua. Pilih Divisi lain atau kosongkan.');
                            }
                        }
                    }
                ])
            ],
            'kuota_cuti' => 'nullable|integer|min:0',
        ]);

        $kuota = $request->kuota_cuti;
        if ($request->role === 'hrd' || $request->role === 'admin') {
            $kuota = 0;
        } elseif ($kuota === null) {
            $kuota = 12;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'divisi_id' => $request->divisi_id,
            'kuota_cuti' => $kuota,
        ]);

        // Tambahan Logika: Jika role adalah Ketua Divisi dan Divisi dipilih, update kolom Ketua Divisi di tabel Divisi
        if ($request->role === 'ketua_divisi' && $request->divisi_id) {
            Divisi::where('id', $request->divisi_id)->update(['ketua_divisi_id' => $user->id]);
        }

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $divisi = Divisi::all();
        
        // 1. Base Role (Tanpa Admin)
        $roles = ['ketua_divisi', 'user'];

        // 2. Logika HRD Cerdas untuk Edit:
        $hrdLainExists = User::where('role', 'hrd')->where('id', '!=', $user->id)->exists();

        if (!$hrdLainExists) {
             array_unshift($roles, 'hrd');
        }

        return view('users.edit', compact('user', 'divisi', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Ambil ID Divisi yang saat ini dipimpin oleh user ini
        $currentManagedDivisiId = $user->divisiKetua->id ?? null;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], 
            'role' => ['required', Rule::in(['admin', 'hrd', 'ketua_divisi', 'user'])],
            'divisi_id' => [
                'nullable', 
                'exists:divisis,id',
                // VALIDASI BARU: Jika user adalah Ketua Divisi, pastikan Divisi tsb belum punya Ketua lain
                Rule::when($request->role === 'ketua_divisi', [
                    function ($attribute, $value, $fail) use ($user, $currentManagedDivisiId) {
                        if ($value) {
                            $divisi = Divisi::find($value);
                            // Cek jika Divisi yang dipilih sudah punya ketua yang berbeda dengan user yang sedang diedit
                            if ($divisi->ketua_divisi_id !== null && $divisi->ketua_divisi_id !== $user->id) {
                                $fail('Divisi ini sudah memiliki Ketua. Pilih Divisi lain atau kosongkan.');
                            }
                        }
                    }
                ])
            ],
            'kuota_cuti' => 'required|integer|min:0',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only(['name', 'email', 'role', 'divisi_id', 'kuota_cuti']);

        // 1. Logika Kuota
        if ($request->role === 'hrd' || $request->role === 'admin') {
            $data['kuota_cuti'] = 0;
        }

        // 2. Logika Password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        // 3. Logika Update Divisi Ketua
        // Cek apakah Divisi ID di form berubah atau role berubah menjadi non-ketua
        if ($user->role === 'ketua_divisi' && $currentManagedDivisiId && $currentManagedDivisiId != $request->divisi_id) {
            // Hapus status kepemimpinan di Divisi lama
            Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }
        
        // Update user
        $user->update($data);

        // 4. Update status kepemimpinan di tabel Divisi
        if ($user->role === 'ketua_divisi' && $request->divisi_id) {
            // Set user ini sebagai ketua di Divisi yang baru dipilih
            Divisi::where('id', $request->divisi_id)->update(['ketua_divisi_id' => $user->id]);
        }
        
        // 5. Final Check: Jika role diubah menjadi non-ketua, pastikan Divisi yang pernah dia pimpin di-reset
        if ($user->role !== 'ketua_divisi') {
             Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }


        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) return back()->with('error', 'Gagal hapus akun sendiri.');
        
        // Reset Divisi Ketua jika user yang dihapus adalah Ketua Divisi
        if ($user->role === 'ketua_divisi') {
            Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User dihapus.');
    }
}