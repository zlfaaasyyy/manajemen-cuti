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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhereHas('ketuaDivisi', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sort = $request->get('sort', 'nama_asc');
        switch ($sort) {
            case 'nama_asc': $query->orderBy('nama', 'asc'); break;
            case 'nama_desc': $query->orderBy('nama', 'desc'); break;
            case 'anggota_banyak': $query->orderBy('users_count', 'desc'); break;
            case 'anggota_sedikit': $query->orderBy('users_count', 'asc'); break;
            case 'terbaru': $query->orderBy('created_at', 'desc'); break;
            case 'terlama': $query->orderBy('created_at', 'asc'); break; 
            default: $query->orderBy('nama', 'asc'); break;
        }

        $divisis = $query->get();
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
        
        if ($user->divisi_id && $user->divisi_id != $divisi->id) { 
            return back()->with('error', 'User tersebut sudah memiliki divisi lain.');
        }

        $user->update(['divisi_id' => $divisi->id]);

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function removeMember(User $user)
    {
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
        
        $oldLeaderId = $divisi->ketua_divisi_id;
        $newLeaderId = $request->ketua_divisi_id;

        $divisi->update($request->all());

        if ($oldLeaderId !== $newLeaderId) {
            if ($oldLeaderId) {
                User::where('id', $oldLeaderId)->update(['divisi_id' => null]);
            }
        }
        User::where('id', $newLeaderId)->update(['divisi_id' => $divisi->id]);

        return redirect()->route('divisi.index')->with('success', 'Divisi diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        User::where('divisi_id', $divisi->id)->update(['divisi_id' => null]);
        $divisi->update(['ketua_divisi_id' => null]); 
        
        $divisi->delete();
        return redirect()->route('divisi.index')->with('success', 'Divisi dihapus.');
    }
}