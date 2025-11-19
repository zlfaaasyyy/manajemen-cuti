<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Lengkap
            
            //  Username wajib ada & unik
            $table->string('username')->unique()->nullable(); 
            
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            //  4 Level pengguna: Admin, User, Ketua Divisi, HRD
            $table->enum('role', ['admin', 'user', 'ketua_divisi', 'hrd'])->default('user');

            // [cite: 165] Data profil tambahan
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();

            // [cite: 66, 170] Kuota cuti default 12 hari
            $table->integer('kuota_cuti')->default(12);

            // [cite: 62] Status aktif/tidak aktif untuk filter user
            $table->boolean('is_active')->default(true);

            // [cite: 108] Relasi ke Divisi
            // Note: Kita tidak pakai 'constrained()' disini agar tidak error 
            // jika tabel 'divisis' belum dibuat saat migration berjalan.
            $table->foreignId('divisi_id')->nullable()->index(); 

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};