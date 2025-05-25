<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project', function (Blueprint $table) {
            $table->enum('send_notif_project_alumni', ['Yes', 'No'])->default('Yes')->after('status')->comment('Status kirim notifikasi ke alumni per project');
            $table->enum('send_notif_project_atasan', ['Yes', 'No'])->default('Yes')->after('send_notif_project_alumni')->comment('Status kirim notifikasi ke atasan per project');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropColumn('send_notif_project_alumni');
            $table->dropColumn('send_notif_project_atasan');
        });
    }
};
