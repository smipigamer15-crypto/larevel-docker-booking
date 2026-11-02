<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('security_inquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('security_inquiries', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable();
                $table->string('stripe_subscription_id')->nullable();
                $table->string('stripe_price_id')->nullable();
                $table->string('subscription_status')->nullable();
                $table->timestamp('subscription_ends_at')->nullable();
                $table->boolean('cancel_at_period_end')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('security_inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_subscription_id',
                'stripe_price_id',
                'subscription_status',
                'subscription_ends_at',
                'cancel_at_period_end',
            ]);
        });
    }
};
