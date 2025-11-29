<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisis';

    protected $fillable = [
        'nama',
        'ketua_divisi_id',
        'deskripsi',
    ];

    public function ketuaDivisi()
    {
        return $this->belongsTo(User::class, 'ketua_divisi_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'divisi_id');
    }
}