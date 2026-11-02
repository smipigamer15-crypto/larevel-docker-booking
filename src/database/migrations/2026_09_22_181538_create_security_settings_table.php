<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('ip_whitelist')->nullable();
            $table->json('ip_blacklist')->nullable();
            $table->json('blocked_countries')->nullable();
            $table->integer('max_login_attempts')->default(5);
            $table->integer('login_lockout_minutes')->default(15);
            $table->integer('session_timeout_minutes')->default(120);
            $table->boolean('require_2fa')->default(false);
            $table->json('allowed_hours')->nullable();
            $table->json('allowed_days')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};
