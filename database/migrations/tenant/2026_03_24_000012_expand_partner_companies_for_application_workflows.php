<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('partner_companies', function (Blueprint $table) {
            $table->text('available_positions')->nullable()->after('industry');
            $table->text('required_documents')->nullable()->after('available_positions');
        });
    }

    public function down(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('partner_companies', function (Blueprint $table) {
            $table->dropColumn([
                'available_positions',
                'required_documents',
            ]);
        });
    }
};
