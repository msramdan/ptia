<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_log_send_notif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_responden_id')->constrained('project_responden')->onDelete('cascade');
            $table->string('telepon');
            $table->enum('remark', ['Alumni', 'Atasan']);
            $table->enum('status', ['Sukses', 'Gagal']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_log_send_notif');
    }
};
