<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\TenantAdmin;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TenantAuthController extends Controller
{
    public function admin(CurrentTenant $currentTenant): View
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        return view('tenant.auth.login', [
            'tenant' => $tenant,
            'pageTitle' => 'Login | '.config('app.name', 'BukSU Practicum'),
            'loginAction' => $this->loginAction($tenant),
            'registerUrl' => request()->routeIs('tenant.domain.*')
                ? route('tenant.domain.register.create')
                : route('tenant.register.create', $tenant),
        ]);
    }

    public function storeAdmin(Request $request, CurrentTenant $currentTenant): RedirectResponse
    {
        $tenant = $currentTenant->tenant();

        abort_unless($tenant, 404);

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $role = $this->roleForEmail($request->string('email')->toString());

        if (! $role) {
            throw ValidationException::withMessages([
                'email' => 'No account was found for this email in the selected tenant.',
            ]);
        }

        $guard = $this->guardForRole($role);

        if (! Auth::guard($guard)->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $authenticatedUser = Auth::guard($guard)->user();

        if (! $this->canAccessPortal($authenticatedUser, $role)) {
            Auth::guard($guard)->logout();

            throw ValidationException::withMessages([
                'email' => $this->blockedMessage($role),
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put("tenant_context.{$guard}", $tenant->slug);

        return redirect()->to($this->dashboardPath($role, $tenant->matchesDomain($request->getHost()), $tenant->slug));
    }

    public function create(CurrentTenant $currentTenant, string $role): View
    {
        return redirect()->to($this->loginPagePath($currentTenant->tenant()?->matchesDomain(request()->getHost()) ?? false, $currentTenant->tenant()?->slug));
    }

    public function store(Request $request, CurrentTenant $currentTenant, string $role): RedirectResponse
    {
        return $this->storeAdmin($request, $currentTenant);
    }

    public function destroy(Request $request): RedirectResponse
    {
        foreach (['tenant_admin', 'supervisor', 'student'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
                $request->session()->forget("tenant_context.{$guard}");
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (! in_array($request->getHost(), config('tenancy.central_domains', []), true)) {
            return redirect('/login');
        }

        $tenant = app(CurrentTenant::class)->tenant();

        return redirect()->route('tenant.login.default', [
            'tenant' => $tenant?->slug ?? config('tenancy.default_tenant_slug'),
        ]);
    }

    protected function guardForRole(string $role): string
    {
        return match ($role) {
            'admin' => 'tenant_admin',
            'supervisor' => 'supervisor',
            default => 'student',
        };
    }

    protected function loginAction($tenant): string
    {
        if (request()->routeIs('tenant.domain.*')) {
            return route('tenant.domain.login.default.store');
        }

        return route('tenant.login.default.store', [
            'tenant' => $tenant,
        ]);
    }

    protected function roleForEmail(string $email): ?string
    {
        $roles = [
            'admin' => TenantAdmin::query(),
            'supervisor' => Supervisor::query(),
            'student' => Student::query(),
        ];

        foreach ($roles as $role => $query) {
            if ($query->where('email', $email)->exists()) {
                return $role;
            }
        }

        return null;
    }

    protected function canAccessPortal($user, string $role): bool
    {
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'canAccessPortal')) {
            return $user->canAccessPortal();
        }

        return true;
    }

    protected function blockedMessage(string $role): string
    {
        return match ($role) {
            'student' => 'This student account is suspended or still waiting for email verification.',
            'supervisor' => 'This teacher account is suspended or still waiting for email verification.',
            default => 'This admin account is suspended. Please contact the superadmin.',
        };
    }

    protected function dashboardPath(string $role, bool $isDomain, ?string $slug): string
    {
        if ($isDomain) {
            return match ($role) {
                'admin' => route('tenant.domain.admin.dashboard'),
                'supervisor' => route('tenant.domain.supervisor.dashboard'),
                default => route('tenant.domain.student.dashboard'),
            };
        }

        return match ($role) {
            'admin' => route('tenant.admin.dashboard', $slug),
            'supervisor' => route('tenant.supervisor.dashboard', $slug),
            default => route('tenant.student.dashboard', $slug),
        };
    }

    protected function loginPagePath(bool $isDomain, ?string $slug): string
    {
        if ($isDomain) {
            return route('tenant.domain.login.default');
        }

        return route('tenant.login.default', $slug ?? config('tenancy.default_tenant_slug'));
    }
}
