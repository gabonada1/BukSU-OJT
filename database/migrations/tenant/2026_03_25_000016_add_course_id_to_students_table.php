<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->table('students', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->nullable()
                ->after('program')
                ->constrained('courses')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection(config('tenancy.tenant_connection', 'tenant'))->table('students', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};
