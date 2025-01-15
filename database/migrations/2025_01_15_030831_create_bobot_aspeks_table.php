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
        Schema::create('bobot_aspek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspek_id')->constrained('aspek')->restrictOnUpdate()->cascadeOnDelete();
            $table->float('bobot_alumni');
            $table->float('bobot_atasan_langsung');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bobot_aspek');
    }
};
