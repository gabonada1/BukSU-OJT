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
            if (Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'stripe_checkout_session_id')) {
                $table->dropUnique(['stripe_checkout_session_id']);
                $table->dropColumn([
                    'stripe_checkout_session_id',
                    'stripe_payment_intent_id',
                    'stripe_customer_email',
                ]);
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'square_payment_link_id')) {
                $table->string('square_payment_link_id')->nullable()->unique()->after('payment_currency');
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'square_order_id')) {
                $table->string('square_order_id')->nullable()->after('square_payment_link_id');
            }

            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'square_customer_email')) {
                $table->string('square_customer_email')->nullable()->after('square_order_id');
            }
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('tenant_plan_applications', function (Blueprint $table) {
            if (Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'square_payment_link_id')) {
                $table->dropUnique(['square_payment_link_id']);
                $table->dropColumn([
                    'square_payment_link_id',
                    'square_order_id',
                    'square_customer_email',
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
};
