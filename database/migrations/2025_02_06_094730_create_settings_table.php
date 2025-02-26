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
        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aplikasi', 255);
            $table->text('tentang_aplikasi');
            $table->string('logo')->nullable();
            $table->string('logo_login')->nullable();
            $table->string('favicon')->nullable();
            $table->text('pengumuman');
            $table->enum('is_aktif_pengumuman', ['Yes', 'No']);
            $table->time('jam_mulai')->default('07:00:00');
            $table->time('jam_selesai')->default('17:00:00');
            $table->json('hari_libur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
};
