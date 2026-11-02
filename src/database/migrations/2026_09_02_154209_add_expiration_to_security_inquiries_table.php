<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('security_inquiries', function (Blueprint $table) {
            $table->enum('duration', ['1_month', '3_months', '6_months', '1_year', 'custom'])
                ->nullable()
                ->after('package');

            $table->integer('duration_days')
                ->nullable()
                ->after('duration');

            $table->timestamp('expires_at')
                ->nullable()
                ->after('access_activated_at');

            $table->timestamp('last_reminder_sent_at')
                ->nullable()
                ->after('expires_at');

            $table->integer('reminder_count')
                ->default(0)
                ->after('last_reminder_sent_at');
        });
    }

    public function down()
    {
        Schema::table('security_inquiries', function (Blueprint $table) {
            $table->dropColumn(['duration', 'duration_days', 'expires_at', 'last_reminder_sent_at', 'reminder_count']);
        });
    }
};
