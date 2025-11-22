<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'divisis';

    // Kolom yang dapat diisi
    protected $fillable = [
        'nama',
        'ketua_divisi_id', // Foreign key ke tabel users
        'deskripsi',
    ];

    /**
     * Relasi ke User (Ketua Divisi)
     * Satu Divisi memiliki SATU Ketua Divisi (Many-to-One).
     */
    public function ketuaDivisi()
    {
        return $this->belongsTo(User::class, 'ketua_divisi_id');
    }

    /**
     * Relasi ke User (Anggota Divisi)
     * Satu Divisi memiliki BANYAK User/Anggota (One-to-Many).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'divisi_id');
    }
}