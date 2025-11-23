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

        // 1. FITUR FILTER (PENCARIAN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhereHas('ketuaDivisi', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. FITUR SORTIR
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'nama_asc':
                    $query->orderBy('nama', 'asc');
                    break;
                case 'nama_desc':
                    $query->orderBy('nama', 'desc');
                    break;
                case 'anggota_banyak':
                    $query->orderBy('users_count', 'desc'); // Membutuhkan withCount('users')
                    break;
                case 'anggota_sedikit':
                    $query->orderBy('users_count', 'asc');
                    break;
                case 'terbaru':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'terlama':
                    $query->orderBy('created_at', 'asc');
                    break;
            }
        } else {
            // Default sort
            $query->orderBy('nama', 'asc');
        }

        $divisi = $query->get();

        return view('divisi.index', compact('divisi'));
    }

    // FITUR DETAIL DIVISI (Show Members)
    public function show(Divisi $divisi)
    {
        // Muat user yang menjadi anggota divisi ini
        $divisi->load(['users' => function($q) {
            $q->orderBy('name');
        }, 'ketuaDivisi']);

        return view('divisi.show', compact('divisi'));
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
            'ketua_divisi_id' => [
                'required', 
                'exists:users,id',
                Rule::unique('divisis', 'ketua_divisi_id')->where(function ($query) {
                    return $query->whereNotNull('ketua_divisi_id');
                })
            ],
            'deskripsi' => 'nullable|string',
        ]);

        $divisi = Divisi::create([
            'nama' => $request->nama,
            'ketua_divisi_id' => $request->ketua_divisi_id,
            'deskripsi' => $request->deskripsi,
        ]);

        // Auto sync ketua
        $ketua = User::find($request->ketua_divisi_id);
        if ($ketua) {
            $ketua->update(['divisi_id' => $divisi->id]);
        }

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil dibuat.');
    }

    public function edit(Divisi $divisi)
    {
        $currentLeaderId = $divisi->ketua_divisi_id;
        
        $availableLeaders = User::where('role', 'ketua_divisi')
            ->where(function ($query) use ($currentLeaderId) {
                $query->whereDoesntHave('divisiKetua')
                      ->orWhere('id', $currentLeaderId);
            })
            ->get();
            
        return view('divisi.edit', compact('divisi', 'availableLeaders'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('divisis', 'nama')->ignore($divisi->id)], 
            'ketua_divisi_id' => [
                'required', 
                'exists:users,id',
                Rule::unique('divisis', 'ketua_divisi_id')->ignore($divisi->id)
            ],
            'deskripsi' => 'nullable|string',
        ]);

        $divisi->update($request->only(['nama', 'ketua_divisi_id', 'deskripsi']));

        $ketua = User::find($request->ketua_divisi_id);
        if ($ketua) {
            $ketua->update(['divisi_id' => $divisi->id]);
        }

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        // Lepas relasi anggota (set null)
        User::where('divisi_id', $divisi->id)->update(['divisi_id' => null]);
        
        $divisi->delete();

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil dihapus.');
    }
}