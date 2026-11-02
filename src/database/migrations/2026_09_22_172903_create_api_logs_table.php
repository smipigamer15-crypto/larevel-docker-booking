<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('token_id')->nullable();
            $table->string('method', 10);
            $table->string('endpoint');
            $table->integer('status_code');
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('response_time')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('endpoint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
