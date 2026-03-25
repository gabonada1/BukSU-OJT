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
            if ($schema->hasColumn('tenants', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }

    public function down(): void
    {
        $schema = Schema::connection($this->connection);

        $schema->table('tenants', function (Blueprint $table) use ($schema) {
            if (! $schema->hasColumn('tenants', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
        });
    }
};
