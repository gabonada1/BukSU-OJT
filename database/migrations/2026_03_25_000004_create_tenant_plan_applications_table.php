<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection($this->connection)->create('tenant_plan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('college_name');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('admin_email');
            $table->string('selected_plan', 30);
            $table->string('preferred_subdomain', 100)->nullable();
            $table->string('preferred_domain')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_status', 30)->default('pending');
            $table->unsignedInteger('payment_amount')->default(0);
            $table->string('payment_currency', 10)->default('php');
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_customer_email')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('status', 30)->default('pending_payment');
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('central_superadmins')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('tenant_plan_applications');
    }
};
