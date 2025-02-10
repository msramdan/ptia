<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('diklat_type_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('diklat_type_id');
            $table->string('diklatTypeName');
            $table->timestamps();

            $table->foreign('diklat_type_id')->references('id')->on('diklat_type')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diklat_type_mapping');
    }
};
