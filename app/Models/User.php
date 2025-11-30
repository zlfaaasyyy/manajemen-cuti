<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username', 
        'email',
        'password',
        'role',
        'divisi_id', 
        'kuota_cuti',
        'foto_profil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function divisiKetua()
    {
        return $this->hasOne(Divisi::class, 'ketua_divisi_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}