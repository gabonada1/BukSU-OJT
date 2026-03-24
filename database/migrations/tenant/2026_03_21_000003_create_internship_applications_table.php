<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->create('internship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('partner_company_id')->constrained('partner_companies')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->date('applied_at')->nullable();
            $table->date('deployed_at')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->dropIfExists('internship_applications');
    }
};
