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
        Schema::create('project_bobot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->restrictOnUpdate()->cascadeOnDelete();
            $table->char('aspek_id', 20);
            $table->enum('level', ['3', '4']);
            $table->string('aspek', 255);
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
        Schema::dropIfExists('project_bobot');
    }
};
