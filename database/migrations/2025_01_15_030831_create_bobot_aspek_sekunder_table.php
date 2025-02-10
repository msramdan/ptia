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
        Schema::create('bobot_aspek_sekunder', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diklat_type_id');
            $table->float('bobot_aspek_sekunder');
            $table->timestamps();

            $table->foreign('diklat_type_id')->references('id')->on('diklat_type')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bobot_aspek_sekunder');
    }
};
