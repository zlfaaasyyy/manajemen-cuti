<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_cuti',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_hari',
        'alasan',
        'bukti_sakit',
        'status',
        'catatan_leader',
        'catatan_penolakan',
        // TAMBAHAN BARU
        'alamat_selama_cuti', 
        'nomor_darurat'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}