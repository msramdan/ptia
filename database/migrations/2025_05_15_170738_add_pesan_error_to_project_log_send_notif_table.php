<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_log_send_notif', function (Blueprint $table) {
            $table->text('log_pesan')->nullable()->default(null)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('project_log_send_notif', function (Blueprint $table) {
            $table->dropColumn('log_pesan');
        });
    }
};
