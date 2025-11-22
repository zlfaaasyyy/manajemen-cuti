<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // PERHATIAN: HasApiTokens SUDAH DIHAPUS dari sini
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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

    // --- RELASI ELOQUENT ---

    /**
     * Relasi ke Divisi (User adalah Anggota)
     * User ini adalah anggota dari SATU Divisi (Many-to-One).
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    /**
     * Relasi ke Divisi (User adalah Ketua)
     * Relasi ini digunakan DivisiController untuk mengecek apakah user sudah menjadi ketua divisi lain.
     */
    public function divisiKetua()
    {
        // Mencari Divisi di mana kolom 'ketua_divisi_id' sama dengan ID user ini.
        return $this->hasOne(Divisi::class, 'ketua_divisi_id');
    }

    /**
     * Relasi ke LeaveRequest (Pengajuan Cuti)
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}