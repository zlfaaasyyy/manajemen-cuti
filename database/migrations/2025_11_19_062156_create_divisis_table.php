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
        Schema::create('divisis', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); 
            $table->text('deskripsi')->nullable(); 
            
            // Kita gunakan unsignedBigInteger dulu untuk ketua
            // Relasi diatur belakangan agar tidak error
            $table->unsignedBigInteger('ketua_divisi_id')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisis');
    }
};