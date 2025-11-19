<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    // 1. Menampilkan Daftar Divisi
    public function index()
    {
        // Ambil data divisi beserta ketuanya
        $divisis = Divisi::with('ketua')->get();
        return view('divisi.index', compact('divisis'));
    }

    // 2. Menampilkan Form Tambah Divisi
    public function create()
    {
        // Ambil user yang role-nya 'ketua_divisi' untuk dijadikan calon ketua
        $calonKetua = User::where('role', 'ketua_divisi')->get();
        return view('divisi.create', compact('calonKetua'));
    }

    // 3. Menyimpan Data ke Database
    public function store(Request $request)
    {
        // Validasi input [cite: 89, 90]
        $request->validate([
            'nama' => 'required|unique:divisis,nama',
            'ketua_divisi_id' => 'required|exists:users,id', // Wajib pilih ketua
            'deskripsi' => 'nullable'
        ]);

        // Simpan ke database
        Divisi::create($request->all());

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil dibuat!');
    }

    // 4. Menghapus Divisi (Nanti kita pakai tombol delete)
    public function destroy(Divisi $divisi)
    {
        $divisi->delete();
        return redirect()->route('divisi.index')->with('success', 'Divisi dihapus!');
    }
}