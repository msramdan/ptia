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
        Schema::create('project_data_sekunder', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->restrictOnUpdate()->cascadeOnDelete();
            $table->bigInteger('nilai_kinerja_awal');
            $table->string('periode_awal', 150);
            $table->bigInteger('nilai_kinerja_akhir');
            $table->string('periode_akhir', 150);
            $table->enum('satuan', ['Persentase (%)', 'Skor', 'Rupiah (Rp)', 'Waktu (Jam)', 'Waktu (Hari)', 'Pcs', 'Unit', 'Item', 'Dollar (USD)', 'Index']);
            $table->string('sumber_data', 150);
            $table->string('unit_kerja', 255);
            $table->string('nama_pic', 150);
            $table->string('telpon', 15);
            $table->text('keterangan')->nullable();
            $table->string('berkas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_data_sekunder');
    }
};
