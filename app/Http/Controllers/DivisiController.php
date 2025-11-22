<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisiController extends Controller
{
    // Tampilkan daftar semua divisi
    public function index()
    {
        // Mengambil data divisi beserta ketua dan anggotanya
        $divisi = Divisi::with('ketuaDivisi', 'users')->get()->map(function ($div) {
            $div->jumlah_anggota = $div->users->count();
            return $div;
        });
        return view('divisi.index', compact('divisi'));
    }

    // Tampilkan form tambah divisi
    public function create()
    {
        // Ambil user role 'ketua_divisi' yang belum punya divisi
        $availableLeaders = User::where('role', 'ketua_divisi')
            ->whereDoesntHave('divisiKetua') 
            ->get();
            
        return view('divisi.create', compact('availableLeaders'));
    }

    // Proses simpan divisi baru
    public function store(Request $request)
    {
        $request->validate([
            // PERBAIKAN PENTING: Gunakan 'divisis' (jamak), bukan 'divisi'
            'nama' => 'required|string|max:255|unique:divisis,nama',
            'ketua_divisi_id' => [
                'required', 
                'exists:users,id',
                // PERBAIKAN PENTING: Gunakan 'divisis'
                Rule::unique('divisis', 'ketua_divisi_id')->where(function ($query) {
                    return $query->whereNotNull('ketua_divisi_id');
                })
            ],
            'deskripsi' => 'nullable|string',
        ]);

        Divisi::create([
            'nama' => $request->nama,
            'ketua_divisi_id' => $request->ketua_divisi_id,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('divisi.index')->with('success', 'Divisi baru berhasil ditambahkan!');
    }

    // Tampilkan form edit divisi
    public function edit(Divisi $divisi)
    {
        // Ambil user role 'ketua_divisi' yang belum punya divisi ATAU dia adalah ketua divisi ini
        $currentLeaderId = $divisi->ketua_divisi_id;
        
        $availableLeaders = User::where('role', 'ketua_divisi')
            ->where(function ($query) use ($currentLeaderId) {
                $query->whereDoesntHave('divisiKetua')
                      ->orWhere('id', $currentLeaderId);
            })
            ->get();
            
        return view('divisi.edit', compact('divisi', 'availableLeaders'));
    }

    // Proses update divisi
    public function update(Request $request, Divisi $divisi)
    {
        $request->validate([
            // PERBAIKAN PENTING: Ubah 'divisi' menjadi 'divisis' di sini!
            'nama' => ['required', 'string', 'max:255', Rule::unique('divisis', 'nama')->ignore($divisi->id)], 
            
            'ketua_divisi_id' => [
                'required', 
                'exists:users,id',
                // PERBAIKAN PENTING: Ubah 'divisi' menjadi 'divisis' di sini juga!
                Rule::unique('divisis', 'ketua_divisi_id')->ignore($divisi->id)
            ],
            'deskripsi' => 'nullable|string',
        ]);

        $divisi->update($request->only(['nama', 'ketua_divisi_id', 'deskripsi']));

        return redirect()->route('divisi.index')->with('success', 'Data divisi berhasil diperbarui!');
    }

    // Proses hapus divisi
    public function destroy(Divisi $divisi)
    {
        // Lepaskan user dari divisi sebelum dihapus
        User::where('divisi_id', $divisi->id)->update(['divisi_id' => null]);
        
        $divisi->delete();

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil dihapus.');
    }
}