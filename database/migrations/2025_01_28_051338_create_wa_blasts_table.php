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
        Schema::create('wa_blast', function (Blueprint $table) {
            $table->id();
            $table->string('session_name', 255);
			$table->string('whatsapp_number')->nullable();
			$table->enum('status', ['CONNECTED', 'STOPPED']);
			$table->text('webhook')->nullable();
			$table->string('api_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_blast');
    }
};
