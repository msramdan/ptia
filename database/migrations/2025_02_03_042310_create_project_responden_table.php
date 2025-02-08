<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('project_responden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->restrictOnUpdate()->cascadeOnDelete();
            $table->string('peserta_id');
            $table->string('nama');
            $table->string('nip');
            $table->string('telepon')->nullable();
            $table->string('jabatan');
            $table->string('unit');
            $table->decimal('nilai_pre_test', 5, 2)->nullable();
            $table->decimal('nilai_post_test', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_responden');
    }
};
