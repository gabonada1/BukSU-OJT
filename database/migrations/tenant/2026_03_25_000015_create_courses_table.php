<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->decimal('required_ojt_hours', 6, 2)->default(486.00);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->dropIfExists('courses');
    }
};
