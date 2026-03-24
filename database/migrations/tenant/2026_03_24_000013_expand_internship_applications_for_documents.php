<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('internship_applications', function (Blueprint $table) {
            $table->string('position_applied')->nullable()->after('partner_company_id');
            $table->string('resume_path')->nullable()->after('position_applied');
            $table->string('endorsement_letter_path')->nullable()->after('resume_path');
            $table->string('moa_path')->nullable()->after('endorsement_letter_path');
            $table->string('clearance_path')->nullable()->after('moa_path');
            $table->text('student_notes')->nullable()->after('clearance_path');
            $table->timestamp('reviewed_at')->nullable()->after('admin_feedback');
        });
    }

    public function down(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');

        Schema::connection($connection)->table('internship_applications', function (Blueprint $table) {
            $table->dropColumn([
                'position_applied',
                'resume_path',
                'endorsement_letter_path',
                'moa_path',
                'clearance_path',
                'student_notes',
                'reviewed_at',
            ]);
        });
    }
};
