<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Divisi::with('ketuaDivisi')->withCount('users');

<<<<<<< HEAD
        // Filter Pencarian (Berdasarkan nama divisi atau ketua divisi)
=======
>>>>>>> 34911a05c55ea847ea8129b7d6dfce84fbd27732
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhereHas('ketuaDivisi', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

<<<<<<< HEAD
        // Filter Sortir (Disempurnakan untuk mencakup tanggal pembuatan)
        $sort = $request->get('sort', 'nama_asc');
        switch ($sort) {
            case 'nama_asc': $query->orderBy('nama', 'asc'); break;
            case 'nama_desc': $query->orderBy('nama', 'desc'); break;
            case 'anggota_banyak': $query->orderBy('users_count', 'desc'); break;
            case 'anggota_sedikit': $query->orderBy('users_count', 'asc'); break;
            case 'terbaru': $query->orderBy('created_at', 'desc'); break;
            case 'terlama': $query->orderBy('created_at', 'asc'); break; // [BARU] Sortir berdasarkan tanggal terlama
            default: $query->orderBy('nama', 'asc'); break;
=======
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'nama_asc': $query->orderBy('nama', 'asc'); break;
                case 'nama_desc': $query->orderBy('nama', 'desc'); break;
                case 'anggota_banyak': $query->orderBy('users_count', 'desc'); break;
                case 'anggota_sedikit': $query->orderBy('users_count', 'asc'); break;
                case 'terbaru': $query->orderBy('created_at', 'desc'); break;
            }
        } else {
            $query->orderBy('nama', 'asc');
>>>>>>> 34911a05c55ea847ea8129b7d6dfce84fbd27732
        }

        $divisis = $query->get();
        // [BARU] Mengirim $request untuk mempertahankan status filter/sortir
        return view('divisi.index', compact('divisis', 'request'));
    }

    public function show(Divisi $divisi)
    {
        $divisi->load(['users' => function($q) {
            $q->orderBy('name');
        }, 'ketuaDivisi']);

        $potentialMembers = User::where('role', 'user')
            ->whereNull('divisi_id')
            ->orderBy('name')
            ->get();

        return view('divisi.show', compact('divisi', 'potentialMembers'));
    }

    public function addMember(Request $request, Divisi $divisi)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::find($request->user_id);
        
        // [KOREKSI] Cek apakah user sudah ketua di divisi lain (logika ini lebih cocok di update user, tapi tetap dipertahankan untuk jaga-jaga)
        if ($user->divisi_id && $user->divisi_id != $divisi->id) { 
            return back()->with('error', 'User tersebut sudah memiliki divisi lain.');
        }

        $user->update(['divisi_id' => $divisi->id]);

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function removeMember(User $user)
    {
        // [KOREKSI] Mencegah mengeluarkan user jika dia adalah Ketua divisi yang bersangkutan (meskipun sudah dicek di view)
        if ($user->divisiKetua && $user->divisiKetua->ketua_divisi_id == $user->id) {
            return back()->with('error', 'Ketua divisi tidak dapat dikeluarkan. Silakan ganti ketua divisi terlebih dahulu.');
        }
        
        $user->update(['divisi_id' => null]);
        return back()->with('success', 'Anggota berhasil dikeluarkan dari divisi.');
    }

    public function create()
    {
        $availableLeaders = User::where('role', 'ketua_divisi')
            ->whereDoesntHave('divisiKetua') 
            ->get();
        return view('divisi.create', compact('availableLeaders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:divisis,nama',
            'ketua_divisi_id' => ['required', 'exists:users,id'],
            'deskripsi' => 'nullable|string',
        ]);

        $divisi = Divisi::create($request->all());

        User::find($request->ketua_divisi_id)->update(['divisi_id' => $divisi->id]);

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil dibuat.');
    }

    public function edit(Divisi $divisi)
    {
        $currentLeaderId = $divisi->ketua_divisi_id;
        // [KOREKSI] Logika: Ambil semua Ketua Divisi, yang tidak sedang memimpin OR ID-nya sama dengan Ketua Divisi saat ini.
        $availableLeaders = User::where('role', 'ketua_divisi')
            ->where(function ($query) use ($currentLeaderId) {
                $query->whereDoesntHave('divisiKetua')->orWhere('id', $currentLeaderId);
            })->get();
            
        return view('divisi.edit', compact('divisi', 'availableLeaders'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        $request->validate([
            'nama' => ['required', Rule::unique('divisis', 'nama')->ignore($divisi->id)], 
            'ketua_divisi_id' => ['required', 'exists:users,id'],
            'deskripsi' => 'nullable|string',
        ]);
        
        // 1. Cek apakah ketua divisi berubah
        $oldLeaderId = $divisi->ketua_divisi_id;
        $newLeaderId = $request->ketua_divisi_id;

        // 2. Update Divisi
        $divisi->update($request->all());

        // 3. Update relasi di tabel Users
        if ($oldLeaderId !== $newLeaderId) {
            // Hapus relasi divisi lama dari ketua lama
            if ($oldLeaderId) {
                User::where('id', $oldLeaderId)->update(['divisi_id' => null]);
            }
        }
        // Pastikan ketua baru terikat ke divisi ini (termasuk jika ketua sama)
        User::where('id', $newLeaderId)->update(['divisi_id' => $divisi->id]);

        return redirect()->route('divisi.index')->with('success', 'Divisi diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        // Set divisi_id semua anggota di divisi ini menjadi NULL
        User::where('divisi_id', $divisi->id)->update(['divisi_id' => null]);
        // [KOREKSI] Reset ketua_divisi_id di tabel divisi itu sendiri (meskipun relasi akan hilang)
        $divisi->update(['ketua_divisi_id' => null]); 
        
        $divisi->delete();
        return redirect()->route('divisi.index')->with('success', 'Divisi dihapus.');
    }
}