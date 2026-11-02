<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blocked_dates')) {
            Schema::table('blocked_dates', function (Blueprint $table) {
                if (! Schema::hasColumn('blocked_dates', 'start_time')) {
                    $table->time('start_time')->nullable();
                }
                if (! Schema::hasColumn('blocked_dates', 'end_time')) {
                    $table->time('end_time')->nullable();
                }
                if (! Schema::hasColumn('blocked_dates', 'all_day')) {
                    $table->boolean('all_day')->default(true);
                }
                if (! Schema::hasColumn('blocked_dates', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasIndex('blocked_dates', ['date'])) {
                    $table->index('date');
                }
            });

            return;
        }

        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->boolean('all_day')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::table('blocked_dates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['start_time', 'end_time', 'all_day']);
            $table->dropIndex(['date']);
        });
    }
};
