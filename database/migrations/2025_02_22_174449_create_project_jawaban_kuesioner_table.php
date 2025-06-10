<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('project_jawaban_kuesioner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_kuesioner_id')
                ->constrained('project_kuesioner')
                ->cascadeOnDelete();
            $table->foreignId('project_responden_id')
                ->constrained('project_responden')
                ->cascadeOnDelete();
            $table->integer('nilai_sebelum')->default(0);
            $table->integer('nilai_sesudah')->default(0);
            $table->integer('nilai_delta')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('remark', ['Alumni', 'Atasan']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_jawaban_kuesioner');
    }
};
