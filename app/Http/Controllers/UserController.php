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
    public function index()
    {
        $now = Carbon::now();

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

        $users = User::whereNotIn('id', $onLeaveIds)
            ->with('divisi')
            ->orderBy('name', 'asc')
            ->paginate(10); 

        return view('users.index', compact('users', 'usersOnLeave'));
    }

    public function create()
    {
        $divisis = Divisi::all();
        
        $roles = ['ketua_divisi', 'user']; 

        if (!User::where('role', 'hrd')->exists()) {
            array_unshift($roles, 'hrd'); 
        }

        if (!User::where('role', 'admin')->exists()) {
            array_unshift($roles, 'admin'); 
        }

        return view('users.create', compact('divisis', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
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
        $hrdExists = User::where('role', 'hrd')->where('id', '!=', $user->id)->exists();
        if (!$hrdExists) {
             array_unshift($roles, 'hrd');
        }

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

        $data = $request->only(['name', 'email', 'role', 'divisi_id']);

        if (in_array($request->role, ['admin', 'hrd'])) {
            $data['kuota_cuti'] = 0;
        } else {
            $data['kuota_cuti'] = $request->kuota_cuti ?? 12;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        if ($user->role === 'ketua_divisi' && $currentManagedDivisiId && $currentManagedDivisiId != $request->divisi_id) {
            Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }
        
        $user->update($data);

        if ($user->role === 'ketua_divisi' && $request->divisi_id) {
            Divisi::where('id', $request->divisi_id)->update(['ketua_divisi_id' => $user->id]);
        }
        
        if ($user->role !== 'ketua_divisi') {
             Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
             return back()->with('error', 'Gagal hapus akun sendiri.');
        }

        if ($user->role === 'hrd') {
            return back()->with('error', 'Akun HRD tidak dapat dihapus karena perannya vital dan harus selalu ada.');
        }
        
        if ($user->role === 'admin') {
            $otherAdmins = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
            if ($otherAdmins === 0) {
                 return back()->with('error', 'Akun Admin tidak dapat dihapus karena tidak ada Admin lain yang tersisa.');
            }
        }

        if ($user->role === 'ketua_divisi') {
            Divisi::where('ketua_divisi_id', $user->id)->update(['ketua_divisi_id' => null]);
        }
        
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}