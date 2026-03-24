<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('student_requirements', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('status');
            $table->text('feedback')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('student_requirements', function (Blueprint $table) {
            $table->dropColumn([
                'file_path',
                'feedback',
            ]);
        });
    }
};
