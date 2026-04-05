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
        $schema = Schema::connection($connection);

        $schema->create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('student_number')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('program')->nullable();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->decimal('required_hours', 6, 2)->default(486);
            $table->decimal('completed_hours', 6, 2)->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('partner_company_id')->nullable()->constrained('partner_companies')->nullOnDelete();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable()->unique();
            $table->timestamp('verification_sent_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->boolean('registered_via_self_service')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->index(['role', 'is_active']);
        });

        $this->copyStudents($connection);
        $this->copyTenantAdmins($connection);
        $this->copySupervisors($connection);
        $this->retargetStudentForeignKeys($connection);

        $schema->dropIfExists('supervisors');
        $schema->dropIfExists('tenant_admins');
        $schema->dropIfExists('students');
    }

    public function down(): void
    {
        $connection = config('tenancy.tenant_connection', 'tenant');
        $schema = Schema::connection($connection);

        $schema->create('tenant_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('suspended_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        $schema->create('supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_company_id')->nullable()->constrained('partner_companies')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable()->unique();
            $table->timestamp('verification_sent_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->boolean('registered_via_self_service')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        $schema->create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('program')->nullable();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->decimal('required_hours', 6, 2)->default(486);
            $table->decimal('completed_hours', 6, 2)->default(0);
            $table->string('status')->default('pending');
            $table->foreignId('partner_company_id')->nullable()->constrained('partner_companies')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable()->unique();
            $table->timestamp('verification_sent_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->boolean('registered_via_self_service')->default(false);
            $table->timestamps();
        });

        $this->restoreSplitTables($connection);
        $this->restoreStudentForeignKeys($connection);

        $schema->dropIfExists('tenant_users');
    }

    protected function copyStudents(string $connection): void
    {
        if (! Schema::connection($connection)->hasTable('students')) {
            return;
        }

        $students = DB::connection($connection)->table('students')->get();

        foreach ($students as $student) {
            DB::connection($connection)->table('tenant_users')->insert([
                'id' => $student->id,
                'role' => 'student',
                'name' => trim($student->first_name.' '.$student->last_name),
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'student_number' => $student->student_number,
                'email' => $student->email,
                'password' => $student->password,
                'program' => $student->program,
                'course_id' => $student->course_id ?? null,
                'required_hours' => $student->required_hours,
                'completed_hours' => $student->completed_hours,
                'status' => $student->status,
                'partner_company_id' => $student->partner_company_id,
                'position' => null,
                'department' => null,
                'is_active' => $student->is_active ?? true,
                'suspended_at' => $student->suspended_at ?? null,
                'email_verified_at' => $student->email_verified_at ?? null,
                'email_verification_token' => $student->email_verification_token ?? null,
                'verification_sent_at' => $student->verification_sent_at ?? null,
                'registered_at' => $student->registered_at ?? null,
                'registered_via_self_service' => $student->registered_via_self_service ?? false,
                'remember_token' => $student->remember_token ?? null,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
            ]);
        }
    }

    protected function copyTenantAdmins(string $connection): void
    {
        if (! Schema::connection($connection)->hasTable('tenant_admins')) {
            return;
        }

        $admins = DB::connection($connection)->table('tenant_admins')->get();

        foreach ($admins as $admin) {
            DB::connection($connection)->table('tenant_users')->insert([
                'role' => 'admin',
                'name' => $admin->name,
                'first_name' => null,
                'last_name' => null,
                'student_number' => null,
                'email' => $admin->email,
                'password' => $admin->password,
                'program' => null,
                'course_id' => null,
                'required_hours' => 486,
                'completed_hours' => 0,
                'status' => 'active',
                'partner_company_id' => null,
                'position' => null,
                'department' => null,
                'is_active' => $admin->is_active ?? true,
                'suspended_at' => $admin->suspended_at ?? null,
                'email_verified_at' => $admin->created_at ?? now(),
                'email_verification_token' => null,
                'verification_sent_at' => null,
                'registered_at' => $admin->created_at ?? now(),
                'registered_via_self_service' => false,
                'remember_token' => $admin->remember_token ?? null,
                'created_at' => $admin->created_at,
                'updated_at' => $admin->updated_at,
            ]);
        }
    }

    protected function copySupervisors(string $connection): void
    {
        if (! Schema::connection($connection)->hasTable('supervisors')) {
            return;
        }

        $supervisors = DB::connection($connection)->table('supervisors')->get();

        foreach ($supervisors as $supervisor) {
            DB::connection($connection)->table('tenant_users')->insert([
                'role' => 'supervisor',
                'name' => $supervisor->name,
                'first_name' => null,
                'last_name' => null,
                'student_number' => null,
                'email' => $supervisor->email,
                'password' => $supervisor->password,
                'program' => null,
                'course_id' => null,
                'required_hours' => 486,
                'completed_hours' => 0,
                'status' => 'active',
                'partner_company_id' => $supervisor->partner_company_id,
                'position' => $supervisor->position ?? null,
                'department' => $supervisor->department ?? null,
                'is_active' => $supervisor->is_active ?? true,
                'suspended_at' => $supervisor->suspended_at ?? null,
                'email_verified_at' => $supervisor->email_verified_at ?? null,
                'email_verification_token' => $supervisor->email_verification_token ?? null,
                'verification_sent_at' => $supervisor->verification_sent_at ?? null,
                'registered_at' => $supervisor->registered_at ?? null,
                'registered_via_self_service' => $supervisor->registered_via_self_service ?? false,
                'remember_token' => $supervisor->remember_token ?? null,
                'created_at' => $supervisor->created_at,
                'updated_at' => $supervisor->updated_at,
            ]);
        }
    }

    protected function retargetStudentForeignKeys(string $connection): void
    {
        Schema::connection($connection)->table('internship_applications', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::connection($connection)->table('student_requirements', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::connection($connection)->table('ojt_hour_logs', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::connection($connection)->table('internship_applications', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('tenant_users')->cascadeOnDelete();
        });

        Schema::connection($connection)->table('student_requirements', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('tenant_users')->cascadeOnDelete();
        });

        Schema::connection($connection)->table('ojt_hour_logs', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('tenant_users')->cascadeOnDelete();
        });
    }

    protected function restoreSplitTables(string $connection): void
    {
        $users = DB::connection($connection)->table('tenant_users')->get();

        foreach ($users as $user) {
            if ($user->role === 'admin') {
                DB::connection($connection)->table('tenant_admins')->insert([
                    'id' => $user->id,
                    'name' => $user->name ?? trim(($user->first_name ?? '').' '.($user->last_name ?? '')),
                    'email' => $user->email,
                    'password' => $user->password,
                    'is_active' => $user->is_active,
                    'suspended_at' => $user->suspended_at,
                    'remember_token' => $user->remember_token,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);

                continue;
            }

            if ($user->role === 'supervisor') {
                DB::connection($connection)->table('supervisors')->insert([
                    'id' => $user->id,
                    'partner_company_id' => $user->partner_company_id,
                    'name' => $user->name ?? trim(($user->first_name ?? '').' '.($user->last_name ?? '')),
                    'email' => $user->email,
                    'position' => $user->position,
                    'department' => $user->department,
                    'password' => $user->password,
                    'is_active' => $user->is_active,
                    'suspended_at' => $user->suspended_at,
                    'email_verified_at' => $user->email_verified_at,
                    'email_verification_token' => $user->email_verification_token,
                    'verification_sent_at' => $user->verification_sent_at,
                    'registered_at' => $user->registered_at,
                    'registered_via_self_service' => $user->registered_via_self_service,
                    'remember_token' => $user->remember_token,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);

                continue;
            }

            DB::connection($connection)->table('students')->insert([
                'id' => $user->id,
                'student_number' => $user->student_number,
                'first_name' => $user->first_name ?? $user->name,
                'last_name' => $user->last_name ?? 'Student',
                'email' => $user->email,
                'password' => $user->password,
                'program' => $user->program,
                'course_id' => $user->course_id,
                'required_hours' => $user->required_hours,
                'completed_hours' => $user->completed_hours,
                'status' => $user->status,
                'partner_company_id' => $user->partner_company_id,
                'is_active' => $user->is_active,
                'suspended_at' => $user->suspended_at,
                'email_verified_at' => $user->email_verified_at,
                'email_verification_token' => $user->email_verification_token,
                'verification_sent_at' => $user->verification_sent_at,
                'registered_at' => $user->registered_at,
                'registered_via_self_service' => $user->registered_via_self_service,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        }
    }

    protected function restoreStudentForeignKeys(string $connection): void
    {
        Schema::connection($connection)->table('internship_applications', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::connection($connection)->table('student_requirements', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::connection($connection)->table('ojt_hour_logs', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });

        Schema::connection($connection)->table('internship_applications', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        Schema::connection($connection)->table('student_requirements', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        Schema::connection($connection)->table('ojt_hour_logs', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });
    }
};
