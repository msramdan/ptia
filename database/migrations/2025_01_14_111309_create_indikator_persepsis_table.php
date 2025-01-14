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
        Schema::create('indikator_persepsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspek_id')->constrained('aspek')->restrictOnUpdate()->cascadeOnDelete();
            $table->enum('indikator_persepsi', ['1', '2', '3', '4']);
            $table->enum('kriteria_persepsi', ['Sangat tidak setuju', 'Tidak setuju', 'Setuju', 'Sangat setuju']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_persepsi');
    }
};
