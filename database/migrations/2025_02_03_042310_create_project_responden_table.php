<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('project_responden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->restrictOnUpdate()->cascadeOnDelete();
            $table->string('peserta_id');
            $table->string('nama');
            $table->string('nip');
            $table->string('telepon')->nullable();
            $table->string('jabatan');
            $table->string('unit');
            $table->decimal('nilai_pre_test', 5, 2)->nullable();
            $table->decimal('nilai_post_test', 5, 2)->nullable();

            $table->integer('try_send_wa_alumni')->default(0);
            $table->enum('status_pengisian_kuesioner_alumni', ['Sudah', 'Belum'])->default('Belum');
            $table->timestamp('last_send_alumni_at')->nullable();
            $table->date('deadline_pengisian_alumni')->nullable(); // Hanya tanggal, tanpa jam

            // Data atasan
            $table->string('nip_atasan')->nullable();
            $table->string('nama_atasan')->nullable();
            $table->string('telepon_atasan')->nullable();
            $table->integer('try_send_wa_atasan')->default(0);
            $table->enum('status_pengisian_kuesioner_atasan', ['Sudah', 'Belum'])->default('Belum');
            $table->timestamp('last_send_atasan_at')->nullable();
            $table->date('deadline_pengisian_atasan')->nullable(); // Hanya tanggal, tanpa jam

            $table->timestamps();
        });
    }



    public function down()
    {
        Schema::dropIfExists('project_responden');
    }
};
