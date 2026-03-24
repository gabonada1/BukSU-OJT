<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('supervisors', function (Blueprint $table) {
            $table->string('department')->nullable()->after('position');
            $table->timestamp('email_verified_at')->nullable()->after('suspended_at');
            $table->string('email_verification_token')->nullable()->unique()->after('email_verified_at');
            $table->timestamp('verification_sent_at')->nullable()->after('email_verification_token');
            $table->timestamp('registered_at')->nullable()->after('verification_sent_at');
            $table->boolean('registered_via_self_service')->default(false)->after('registered_at');
        });

        DB::connection($connection)
            ->table('supervisors')
            ->whereNull('email_verified_at')
            ->update([
                'email_verified_at' => now(),
                'registered_at' => now(),
            ]);
    }

    public function down(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('supervisors', function (Blueprint $table) {
            $table->dropUnique(['email_verification_token']);
            $table->dropColumn([
                'department',
                'email_verified_at',
                'email_verification_token',
                'verification_sent_at',
                'registered_at',
                'registered_via_self_service',
            ]);
        });
    }
};
