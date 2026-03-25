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

        $schema->table('central_superadmins', function (Blueprint $table) use ($schema) {
            if (! $schema->hasColumn('central_superadmins', 'settings')) {
                $table->json('settings')->nullable()->after('password');
            }
        });
    }

    public function down(): void
    {
        $schema = Schema::connection($this->connection);

        $schema->table('central_superadmins', function (Blueprint $table) use ($schema) {
            if ($schema->hasColumn('central_superadmins', 'settings')) {
                $table->dropColumn('settings');
            }
        });
    }
};
