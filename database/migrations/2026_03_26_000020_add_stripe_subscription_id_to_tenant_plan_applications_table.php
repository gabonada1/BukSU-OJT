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
            if (! Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable()->after('stripe_payment_intent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('tenant_plan_applications', function (Blueprint $table) {
            if (Schema::connection($this->connection)->hasColumn('tenant_plan_applications', 'stripe_subscription_id')) {
                $table->dropColumn('stripe_subscription_id');
            }
        });
    }
};
