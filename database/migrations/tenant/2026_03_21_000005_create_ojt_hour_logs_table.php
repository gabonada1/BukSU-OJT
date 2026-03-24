<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->create('ojt_hour_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('log_date');
            $table->decimal('hours', 5, 2);
            $table->text('activity');
            $table->string('status')->default('pending');
            $table->string('supervisor_name')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->dropIfExists('ojt_hour_logs');
    }
};
