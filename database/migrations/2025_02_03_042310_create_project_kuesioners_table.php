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
        Schema::create('project_kuesioner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->restrictOnUpdate()->cascadeOnDelete();
            $table->char('aspek_id', 20);
            $table->enum('level', ['3', '4']);
            $table->string('aspek', 255);
			$table->enum('kriteria', ['Skor Persepsi', 'Delta Skor Persepsi']);
            $table->enum('remark', ['Alumni', 'Atasan']);
			$table->text('pertanyaan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_kuesioner');
    }
};
