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
            if (Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'square_payment_link_id')) {
                $table->dropUnique(['square_payment_link_id']);
                $table->dropColumn([
                    'square_payment_link_id',
                    'square_order_id',
                    'square_customer_email',
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

    public function down(): void
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
};
