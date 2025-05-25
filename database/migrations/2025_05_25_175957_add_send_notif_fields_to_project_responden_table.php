<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('project_responden', function (Blueprint $table) {
            $table->enum('send_notif_alumni', ['Yes', 'No'])->default('Yes')->after('deadline_pengisian_alumni')->comment('Status kirim notifikasi ke alumni per responden');
            $table->enum('send_notif_atasan', ['Yes', 'No'])->default('Yes')->after('deadline_pengisian_atasan')->comment('Status kirim notifikasi ke atasan per responden');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_responden', function (Blueprint $table) {
            $table->dropColumn('send_notif_alumni');
            $table->dropColumn('send_notif_atasan');
        });
    }
};
