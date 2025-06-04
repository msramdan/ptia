<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnIffCronSettingsToSettingTable extends Migration
{
    public function up(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            $table->enum('cron_notif_alumni', ['Yes', 'No'])->default('No')->after('deadline_pengisian');
            $table->enum('cron_notif_atasan', ['Yes', 'No'])->default('No')->after('cron_notif_alumni');
            $table->enum('cron_auto_insert_expired_atasan', ['Yes', 'No'])->default('No')->after('cron_notif_atasan');
            $table->enum('cron_auto_create_project', ['Yes', 'No'])->default('No')->after('cron_auto_insert_expired_atasan');
        });
    }

    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            $table->dropColumn([
                'cron_notif_alumni',
                'cron_notif_atasan',
                'cron_auto_insert_expired_atasan',
                'cron_auto_create_project',
            ]);
        });
    }
}
