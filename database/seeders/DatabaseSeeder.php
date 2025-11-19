<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat 1 Akun Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'kuota_cuti' => 12,
        ]);

        // Buat 1 Akun HRD
        User::create([
            'name' => 'Staff HRD',
            'username' => 'hrd',
            'email' => 'hrd@kantor.com',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
            'kuota_cuti' => 12,
        ]);
    }
}