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
        Schema::create('aspek', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diklat_type_id');
            $table->enum('level', ['3', '4']);
			$table->string('aspek', 255);
            $table->enum('kriteria', ['Skor Persepsi', 'Delta Skor Persepsi']);
			$table->integer('urutan');
            $table->timestamps();

            $table->foreign('diklat_type_id')->references('id')->on('diklat_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspek');
    }
};
