<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Divisi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Divisi punya banyak anggota
    public function members()
    {
        return $this->hasMany(User::class);
    }

    // Divisi dipimpin satu Ketua (User)
    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_divisi_id');
    }
}