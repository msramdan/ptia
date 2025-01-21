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
        Schema::create('kriteria_responden', function (Blueprint $table) {
            $table->id();
            $table->json('nilai_post_test'); // Mengganti enum menjadi JSON untuk mendukung banyak nilai
            $table->float('nilai_pre_test_minimal');
            $table->float('nilai_post_test_minimal');
            $table->float('nilai_kenaikan_pre_post');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria_responden');
    }
};
