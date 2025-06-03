<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsertFromCronToProjectRespondenTable extends Migration
{
    public function up()
    {
        Schema::table('project_responden', function (Blueprint $table) {
            $table->enum('insert_from_cron', ['Yes', 'No'])->default('No')
                ->after('deadline_pengisian_atasan');
        });
    }

    public function down()
    {
        Schema::table('project_responden', function (Blueprint $table) {
            $table->dropColumn('insert_from_cron');
        });
    }
}
