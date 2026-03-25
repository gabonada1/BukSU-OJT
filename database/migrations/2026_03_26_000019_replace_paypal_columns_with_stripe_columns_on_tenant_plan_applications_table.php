<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection($this->connection)->table('tenant_plan_applications', function (Blueprint $table) {
            if (Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'paypal_order_id')) {
                $table->dropUnique(['paypal_order_id']);
                $table->dropColumn([
                    'paypal_order_id',
                    'paypal_capture_id',
                    'paypal_payer_email',
                ]);
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'stripe_checkout_session_id')) {
                $table->string('stripe_checkout_session_id')->nullable()->unique()->after('payment_currency');
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'stripe_payment_intent_id')) {
                $table->string('stripe_payment_intent_id')->nullable()->after('stripe_checkout_session_id');
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'stripe_customer_email')) {
                $table->string('stripe_customer_email')->nullable()->after('stripe_payment_intent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('tenant_plan_applications', function (Blueprint $table) {
            if (Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'stripe_checkout_session_id')) {
                $table->dropUnique(['stripe_checkout_session_id']);
                $table->dropColumn([
                    'stripe_checkout_session_id',
                    'stripe_payment_intent_id',
                    'stripe_customer_email',
                ]);
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'paypal_order_id')) {
                $table->string('paypal_order_id')->nullable()->unique()->after('payment_currency');
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'paypal_capture_id')) {
                $table->string('paypal_capture_id')->nullable()->after('paypal_order_id');
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'paypal_payer_email')) {
                $table->string('paypal_payer_email')->nullable()->after('paypal_capture_id');
            }
        });
    }
};
