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
        Schema::create('project_kriteria_responden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->restrictOnUpdate()->cascadeOnDelete();
			$table->json('nilai_post_test');
			$table->double('nilai_post_test_minimal');
            $table->integer('total_peserta');
            $table->integer('total_termasuk_responden');
            $table->integer('total_tidak_termasuk_responden');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_kriteria_responden');
    }
};
