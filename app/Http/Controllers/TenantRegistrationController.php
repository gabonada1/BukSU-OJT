<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithTenantRouting;
use App\Mail\StudentRegistrationVerificationMail;
use App\Mail\TeacherRegistrationVerificationMail;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\TenantAdmin;
use App\Support\Tenancy\CurrentTenant;
use App\Support\Tenancy\TenantUrlGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TenantRegistrationController extends Controller
{
    use InteractsWithTenantRouting;

    protected array $registrationRoles = ['student', 'teacher'];

    public function create(CurrentTenant $currentTenant): View
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $selectedRole = request()->query('role', old('role'));

        if (! in_array($selectedRole, $this->registrationRoles, true)) {
            $selectedRole = null;
        }

        return view('tenant.auth.register', [
            'tenant' => $tenant,
            'pageTitle' => 'Register | '.config('app.name', 'BukSU Practicum'),
            'selectedRole' => $selectedRole,
            'registerPageUrl' => $this->tenantRoute($tenant, 'register.create'),
            'registerAction' => $this->tenantRoute($tenant, 'register.store'),
            'loginUrl' => $this->tenantRoute($tenant, 'login.default'),
        ]);
    }

    public function store(Request $request, CurrentTenant $currentTenant, TenantUrlGenerator $urlGenerator): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $role = $request->input('role');

        if (! in_array($role, $this->registrationRoles, true)) {
            throw ValidationException::withMessages([
                'role' => 'Choose whether you are registering as a student or teacher.',
            ]);
        }

        if ($role === 'student') {
            $student = $this->registerStudent($request);

            rescue(function () use ($tenant, $student, $urlGenerator) {
                Mail::to($student->email)->send(
                    new StudentRegistrationVerificationMail($tenant, $student, $urlGenerator)
                );
            }, report: true);

            return $this->redirectToTenantRoute(
                $request,
                $tenant,
                'login.default',
                status: 'Student registration received. Please check your email and verify your account before signing in.'
            );
        }

        $teacher = $this->registerTeacher($request);

        rescue(function () use ($tenant, $teacher, $urlGenerator) {
            Mail::to($teacher->email)->send(
                new TeacherRegistrationVerificationMail($tenant, $teacher, $urlGenerator)
            );
        }, report: true);

        return $this->redirectToTenantRoute(
            $request,
            $tenant,
            'login.default',
            status: 'Teacher registration received. Please check your email and verify your account before signing in.'
        );
    }

    public function verify(CurrentTenant $currentTenant, string $token): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $student = Student::query()->where('email_verification_token', $token)->first();
        $message = 'Email verified. You can now sign in to your student dashboard.';

        if (! $student) {
            $teacher = Supervisor::query()->where('email_verification_token', $token)->firstOrFail();
            $teacher->forceFill([
                'email_verified_at' => now(),
                'email_verification_token' => null,
            ])->save();

            $message = 'Email verified. You can now sign in to your teacher workspace.';
        } else {
            $student->forceFill([
                'email_verified_at' => now(),
                'email_verification_token' => null,
            ])->save();
        }

        return redirect()->to($this->tenantRoute($tenant, 'login.default'))
            ->with('status', $message);
    }

    protected function ensureEmailIsAvailable(string $email): void
    {
        $emailTaken = TenantAdmin::query()->where('email', $email)->exists()
            || Student::query()->where('email', $email)->exists()
            || Supervisor::query()->where('email', $email)->exists();

        if ($emailTaken) {
            throw ValidationException::withMessages([
                'email' => 'This email is already being used by another tenant account.',
            ]);
        }
    }

    protected function registerStudent(Request $request): Student
    {
        $data = $request->validate([
            'student_number' => ['required', 'string', 'max:255', 'unique:tenant.students,student_number'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:tenant.students,email'],
            'program' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->ensureEmailIsAvailable($data['email']);

        return Student::query()->create($data + [
            'required_hours' => 486,
            'completed_hours' => 0,
            'status' => 'pending',
            'is_active' => true,
            'email_verification_token' => Str::random(64),
            'verification_sent_at' => now(),
            'registered_at' => now(),
            'registered_via_self_service' => true,
        ]);
    }

    protected function registerTeacher(Request $request): Supervisor
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:tenant.supervisors,email'],
            'department' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->ensureEmailIsAvailable($data['email']);

        return Supervisor::query()->create($data + [
            'is_active' => true,
            'email_verification_token' => Str::random(64),
            'verification_sent_at' => now(),
            'registered_at' => now(),
            'registered_via_self_service' => true,
        ]);
    }
}
