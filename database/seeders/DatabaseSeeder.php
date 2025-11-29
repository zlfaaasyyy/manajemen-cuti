<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Divisi; 
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin123', 
            'email' => 'admin@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'kuota_cuti' => 0, 
        ]);

        $hrd = User::create([
            'name' => 'HRD',
            'username' => 'hrd123', 
            'email' => 'hrd@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
            'kuota_cuti' => 0, 
        ]);

        $ketua_eng = User::create([
            'name' => 'Nur Atika Binti Ardi',
            'username' => 'nuratika',
            'email' => 'nuratika@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'ketua_divisi',
            'kuota_cuti' => 12, 
        ]);

        $divisi_eng = Divisi::create([
            'nama' => 'Engineering & IT',
            'deskripsi' => 'Bertanggung jawab atas infrastruktur dan pengembangan software.',
            'ketua_divisi_id' => $ketua_eng->id,
        ]);
        
        $ketua_eng->update(['divisi_id' => $divisi_eng->id]);

        $ketua_mkt = User::create([
            'name' => 'Nurul Fakhira',
            'username' => 'fakhira',
            'email' => 'fakhira@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'ketua_divisi',
            'kuota_cuti' => 12,
        ]);

        $divisi_mkt = Divisi::create([
            'nama' => 'Marketing & Sales',
            'deskripsi' => 'Bertanggung jawab atas strategi pemasaran dan penjualan produk.',
            'ketua_divisi_id' => $ketua_mkt->id,
        ]);
        
        $ketua_mkt->update(['divisi_id' => $divisi_mkt->id]);

        User::create([
            'name' => 'Daffa Usman',
            'username' => 'daffausman',
            'email' => 'daffausman@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'divisi_id' => $divisi_eng->id,
            'kuota_cuti' => 12,
        ]);

        User::create([
            'name' => 'Akhamd Hidayat',
            'username' => 'yayat',
            'email' => 'yayat@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'divisi_id' => $divisi_mkt->id,
            'kuota_cuti' => 12,
        ]);
        
        User::create([
            'name' => 'Hilmy Afayyad',
            'username' => 'hilmy',
            'email' => 'hilmy@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'kuota_cuti' => 12,
        ]);
    }
}