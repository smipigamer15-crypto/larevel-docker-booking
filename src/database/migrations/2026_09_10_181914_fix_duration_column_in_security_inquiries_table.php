<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('security_inquiries', function (Blueprint $table) {
            $table->string('duration', 50)->nullable()->change();
        });
    }

    public function down() {}
};
