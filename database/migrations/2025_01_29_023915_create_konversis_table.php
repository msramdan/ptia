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
        Schema::create('konversi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diklat_type_id');
            $table->string('jenis_skor', 255);
			$table->integer('skor');
			$table->float('konversi');
            $table->timestamps();

            $table->foreign('diklat_type_id')->references('id')->on('diklat_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konversi');
    }
};
