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
            $table->unsignedBigInteger('diklat_type_id');
            $table->json('nilai_post_test'); // Mengganti enum menjadi JSON untuk mendukung banyak nilai
            $table->float('nilai_post_test_minimal');
            $table->timestamps();

            $table->foreign('diklat_type_id')->references('id')->on('diklat_type')->onDelete('cascade');
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
