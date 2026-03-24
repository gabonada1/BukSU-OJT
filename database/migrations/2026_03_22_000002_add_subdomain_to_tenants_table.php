<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection($this->connection)->table('tenants', function (Blueprint $table) {
            $table->string('subdomain')->nullable()->unique()->after('domain');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('tenants', function (Blueprint $table) {
            $table->dropUnique(['subdomain']);
            $table->dropColumn('subdomain');
        });
    }
};
