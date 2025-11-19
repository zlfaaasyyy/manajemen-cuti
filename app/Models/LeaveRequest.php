<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $guarded = ['id'];

    // Pengajuan milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}