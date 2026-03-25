<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        $schema = Schema::connection($this->connection);

        $schema->table('tenants', function (Blueprint $table) use ($schema) {
            if ($schema->hasColumn('tenants', 'domain')) {
                $table->dropUnique(['domain']);
                $table->dropColumn('domain');
            }

            if ($schema->hasColumn('tenants', 'subdomain')) {
                $table->dropUnique(['subdomain']);
                $table->dropColumn('subdomain');
            }
        });
    }

    public function down(): void
    {
        $schema = Schema::connection($this->connection);

        $schema->table('tenants', function (Blueprint $table) use ($schema) {
            if (! $schema->hasColumn('tenants', 'domain')) {
                $table->string('domain')->nullable()->unique()->after('code');
            }

            if (! $schema->hasColumn('tenants', 'subdomain')) {
                $table->string('subdomain')->nullable()->unique()->after('domain');
            }
        });
    }
};
