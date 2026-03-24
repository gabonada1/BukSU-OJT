<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('tenant_admins', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password');
            $table->timestamp('suspended_at')->nullable()->after('is_active');
        });

        Schema::connection($connection)->table('supervisors', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password');
            $table->timestamp('suspended_at')->nullable()->after('is_active');
        });

        Schema::connection($connection)->table('students', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password');
            $table->timestamp('suspended_at')->nullable()->after('is_active');
            $table->timestamp('email_verified_at')->nullable()->after('suspended_at');
            $table->string('email_verification_token')->nullable()->unique()->after('email_verified_at');
            $table->timestamp('verification_sent_at')->nullable()->after('email_verification_token');
            $table->timestamp('registered_at')->nullable()->after('verification_sent_at');
            $table->boolean('registered_via_self_service')->default(false)->after('registered_at');
        });
    }

    public function down(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('students', function (Blueprint $table) {
            $table->dropUnique(['email_verification_token']);
            $table->dropColumn([
                'is_active',
                'suspended_at',
                'email_verified_at',
                'email_verification_token',
                'verification_sent_at',
                'registered_at',
                'registered_via_self_service',
            ]);
        });

        Schema::connection($connection)->table('supervisors', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'suspended_at']);
        });

        Schema::connection($connection)->table('tenant_admins', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'suspended_at']);
        });
    }
};
