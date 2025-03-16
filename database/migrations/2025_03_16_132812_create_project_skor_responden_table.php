<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('project_skor_responden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->onDelete('cascade');
            $table->foreignId('project_responden_id')->constrained('project_responden')->onDelete('cascade');
            $table->float('skor_level_3_alumni')->nullable();
            $table->float('skor_level_4_alumni')->nullable();
            $table->json('log_data_alumni')->nullable();
            $table->float('skor_level_3_atasan')->nullable();
            $table->float('skor_level_4_atasan')->nullable();
            $table->json('log_data_atasan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_skor_responden');
    }
};
