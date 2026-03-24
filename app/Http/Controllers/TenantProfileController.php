<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithTenantRouting;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\TenantAdmin;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TenantProfileController extends Controller
{
    use InteractsWithTenantRouting;

    public function show(CurrentTenant $currentTenant): View
    {
        [$role, , $user] = $this->currentUser();
        $tenant = $currentTenant->tenant();

        abort_unless($tenant && $user, 404);

        return view('tenant.profile.show', [
            'tenant' => $tenant,
            'pageTitle' => 'Profile | '.config('app.name', 'BukSU Practicum'),
            'profileRole' => $role,
            'profileUser' => $user,
            'profileUpdateAction' => $this->tenantRoute($tenant, 'profile.update'),
            'passwordUpdateAction' => $this->tenantRoute($tenant, 'profile.password.update'),
        ]);
    }

    public function update(Request $request, CurrentTenant $currentTenant): RedirectResponse
    {
        [$role, , $user] = $this->currentUser();
        $tenant = $currentTenant->tenant();

        abort_unless($tenant && $user, 404);

        $data = match ($role) {
            'student' => $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('tenant.students', 'email')->ignore($user->getKey())],
                'program' => ['nullable', 'string', 'max:255'],
            ]),
            'supervisor' => $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('tenant.supervisors', 'email')->ignore($user->getKey())],
                'position' => ['nullable', 'string', 'max:255'],
            ]),
            default => $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('tenant.tenant_admins', 'email')->ignore($user->getKey())],
            ]),
        };

        $this->ensureEmailStaysUniqueAcrossRoles($role, $data['email'], $user->getKey());

        $user->update($data);

        return $this->redirectToTenantRoute($request, $tenant, 'profile.show', status: 'Profile updated.');
    }

    public function updatePassword(Request $request, CurrentTenant $currentTenant): RedirectResponse
    {
        [, $guard, $user] = $this->currentUser();
        $tenant = $currentTenant->tenant();

        abort_unless($tenant && $user, 404);

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => $data['password'],
        ]);

        Auth::guard($guard)->setUser($user->fresh());

        return $this->redirectToTenantRoute($request, $tenant, 'profile.show', status: 'Password updated.');
    }

    protected function currentUser(): array
    {
        if ($user = Auth::guard('tenant_admin')->user()) {
            return ['admin', 'tenant_admin', $this->freshTenantUser($user)];
        }

        if ($user = Auth::guard('supervisor')->user()) {
            return ['supervisor', 'supervisor', $this->freshTenantUser($user)];
        }

        $student = Auth::guard('student')->user();

        return ['student', 'student', $student ? $this->freshTenantUser($student) : null];
    }

    protected function freshTenantUser($user)
    {
        return $user::query()->findOrFail($user->getKey());
    }

    protected function ensureEmailStaysUniqueAcrossRoles(string $role, string $email, int $ignoreId): void
    {
        $conflict = match ($role) {
            'admin' => Supervisor::query()->where('email', $email)->exists() || Student::query()->where('email', $email)->exists(),
            'supervisor' => TenantAdmin::query()->where('email', $email)->exists() || Student::query()->where('email', $email)->exists(),
            default => TenantAdmin::query()->where('email', $email)->exists() || Supervisor::query()->where('email', $email)->exists(),
        };

        if ($conflict) {
            throw ValidationException::withMessages([
                'email' => 'This email is already assigned to another tenant role.',
            ]);
        }
    }
}
