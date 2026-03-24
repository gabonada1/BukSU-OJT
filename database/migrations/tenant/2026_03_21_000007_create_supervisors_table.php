<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->create('supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_company_id')->nullable()->constrained('partner_companies')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('position')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->dropIfExists('supervisors');
    }
};
