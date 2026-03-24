<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        DB::connection($connection)
            ->table('students')
            ->whereNull('email_verified_at')
            ->where('registered_via_self_service', false)
            ->update([
                'email_verified_at' => now(),
                'registered_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
