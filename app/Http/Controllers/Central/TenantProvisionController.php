<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Mail\TenantActivatedMail;
use App\Mail\TenantSubscriptionUpdatedMail;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Tenant;
use App\Models\TenantAdmin;
use App\Models\TenantDomain;
use App\Support\Tenancy\TenantDatabaseManager;
use App\Support\Tenancy\TenantProvisioner;
use App\Support\Tenancy\TenantSubscriptionNotifier;
use App\Support\Tenancy\TenantUrlGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class TenantProvisionController extends Controller
{
    public function __construct(
        protected TenantDatabaseManager $tenantDatabaseManager,
        protected TenantProvisioner $tenantProvisioner,
        protected TenantSubscriptionNotifier $subscriptionNotifier,
        protected TenantUrlGenerator $tenantUrlGenerator,
    ) {
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeTenantManagement();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plan' => ['required', Rule::in(['basic', 'pro', 'premium'])],
            'subscription_starts_at' => ['required', 'date'],
            'subscription_expires_at' => ['nullable', 'date', 'after_or_equal:subscription_starts_at'],
            'subdomain' => ['required', 'alpha_dash', 'max:63'],
            'domain' => ['nullable', 'string', 'max:255'],
            'database' => ['required', 'regex:/^[A-Za-z0-9_]+$/', Rule::unique('central.tenants', 'database')],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ]);
        $domainHosts = $this->resolvedDomainHosts($validated);
        $this->ensureDomainHostsAreAvailable($domainHosts);

        try {
            $tenant = $this->tenantProvisioner->provision([
                'name' => $validated['name'],
                'plan' => $validated['plan'],
                'subscription_starts_at' => $validated['subscription_starts_at'],
                'subscription_expires_at' => $validated['subscription_expires_at'] ?? null,
                'domain_hosts' => $domainHosts,
                'database' => $validated['database'],
                'admin_name' => $validated['admin_name'] ?? null,
                'admin_email' => $validated['admin_email'],
                'admin_password' => $validated['admin_password'] ?? null,
                'settings' => [
                    'provisioned_by' => 'central_superadmin',
                    'branding' => [
                        'portal_title' => 'BukSU Practicum Portal',
                        'accent' => '#7B1C2E',
                        'secondary' => '#F5A623',
                        'logo_path' => null,
                    ],
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput($request->except('admin_password'))
                ->withErrors([
                    'provisioning' => 'College registration failed. Check that MySQL is running and that your central and college database settings in `.env` are correct.',
                ]);
        }

        return redirect()->route('central.dashboard')->with('status', "College {$tenant->name} registered successfully.");
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->authorizeTenantManagement();

        $wasActive = $tenant->is_active;
        $oldPlan = $tenant->plan;
        $oldStartsAt = $tenant->subscription_starts_at?->toDateString();
        $oldExpiresAt = $tenant->subscription_expires_at?->toDateString();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plan' => ['required', Rule::in(['basic', 'pro', 'premium'])],
            'subscription_starts_at' => ['required', 'date'],
            'subscription_expires_at' => ['nullable', 'date', 'after_or_equal:subscription_starts_at'],
            'is_active' => ['required', Rule::in(['0', '1'])],
            'domain_hosts' => ['nullable', 'string', 'max:2000'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
        ]);

        $domainHosts = $this->normalizedDomainHosts($validated['domain_hosts'] ?? '');
        $this->ensureDomainHostsAreAvailable($domainHosts, $tenant);

        $tenant->update([
            'name' => $validated['name'],
            'plan' => $validated['plan'],
            'subscription_starts_at' => $validated['subscription_starts_at'],
            'subscription_expires_at' => $validated['subscription_expires_at'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ]);
        $this->syncDomainHosts($tenant, $domainHosts);
        $this->syncTenantAdminContact($tenant, $validated['admin_email'], $validated['admin_name'] ?? null);

        if (! $tenant->is_active) {
            rescue(fn () => $this->subscriptionNotifier->sendSuspensionNotice($tenant), report: true);
        } elseif (! $wasActive && $tenant->is_active) {
            rescue(fn () => $this->subscriptionNotifier->clearSuspensionNoticeFlag($tenant), report: true);
            rescue(fn () => $this->sendActivationNotice($tenant), report: true);
        }

        if ($this->subscriptionNotifier->shouldWarnForExpiry($tenant)) {
            rescue(function () use ($tenant) {
                $this->subscriptionNotifier->sendExpiryWarning(
                    $tenant,
                    $this->subscriptionNotifier->daysRemaining($tenant)
                );
            }, report: true);
        }

        if (
            $oldPlan !== $tenant->plan
            || $oldStartsAt !== $tenant->subscription_starts_at?->toDateString()
            || $oldExpiresAt !== $tenant->subscription_expires_at?->toDateString()
        ) {
            rescue(fn () => $this->sendSubscriptionUpdatedNotice($tenant), report: true);
        }

        return redirect()
            ->route('central.dashboard', ['section' => 'directory'])
            ->with('status', "College {$tenant->name} updated successfully.");
    }

    public function updateStatus(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->authorizeTenantManagement();

        $validated = $request->validate([
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $tenant->forceFill([
            'is_active' => (bool) $validated['is_active'],
        ])->save();

        if ($tenant->is_active) {
            rescue(fn () => $this->subscriptionNotifier->clearSuspensionNoticeFlag($tenant), report: true);
            rescue(fn () => $this->sendActivationNotice($tenant), report: true);
        } else {
            rescue(fn () => $this->subscriptionNotifier->sendSuspensionNotice($tenant, force: true), report: true);
        }

        return redirect()
            ->route('central.dashboard', ['section' => 'directory'])
            ->with('status', $tenant->name.' is now marked as '.($tenant->is_active ? 'active' : 'suspended').'.');
    }

    public function notify(Request $request, Tenant $tenant): RedirectResponse
    {
        $this->authorizeTenantManagement();

        $validated = $request->validate([
            'notification' => ['required', Rule::in(['activation', 'suspension', 'subscription'])],
        ]);

        $sent = match ($validated['notification']) {
            'activation' => $this->sendActivationNotice($tenant),
            'subscription' => $this->sendSubscriptionUpdatedNotice($tenant),
            default => $this->subscriptionNotifier->sendSuspensionNotice($tenant, force: true),
        };

        return redirect()
            ->route('central.dashboard', ['section' => 'directory', 'edit' => $tenant->getKey()])
            ->with('status', $sent
                ? 'Notification email sent for '.$tenant->name.'.'
                : 'No notification was sent because the tenant has no active coordinator contact.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->authorizeTenantManagement();

        $tenantName = $tenant->name;
        $databaseName = $tenant->database;

        try {
            $this->tenantDatabaseManager->disconnect();
            $this->dropTenantDatabase($databaseName);
            $tenant->delete();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('central.dashboard', ['section' => 'directory'])
                ->withErrors([
                    'provisioning' => "College removal failed for {$tenantName}. Check that MySQL is running and that the database user can drop college databases.",
                ]);
        }

        return redirect()
            ->route('central.dashboard', ['section' => 'directory'])
            ->with('status', "College {$tenantName} and database {$databaseName} deleted successfully.");
    }

    protected function dropTenantDatabase(string $database): void
    {
        $databaseName = str_replace('`', '', $database);

        DB::connection(config('tenancy.central_connection', 'central'))
            ->statement("DROP DATABASE IF EXISTS `{$databaseName}`");
    }

    protected function ensureDomainHostsAreAvailable(array $hosts, ?Tenant $tenant = null): void
    {
        foreach ($hosts as $host) {
            $query = TenantDomain::query()->whereRaw('LOWER(host) = ?', [strtolower($host)]);

            if ($tenant) {
                $query->where('tenant_id', '!=', $tenant->getKey());
            }

            if ($query->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'domain_hosts' => "The host {$host} is already assigned to another tenant.",
                ]);
            }
        }
    }

    protected function resolvedDomainHosts(array $validated): array
    {
        $hosts = [];

        if (filled($validated['domain'] ?? null)) {
            $hosts[] = strtolower(trim((string) $validated['domain']));
        }

        $hosts = array_merge($hosts, $this->tenantUrlGenerator->localAliasHosts(
            $validated['subdomain'] ?? null,
            $validated['name'] ?? null,
            null,
        ));

        return array_values(array_unique(array_filter($hosts)));
    }

    protected function normalizedDomainHosts(string $value): array
    {
        return collect(preg_split('/[\r\n,]+/', $value) ?: [])
            ->map(fn (string $host) => strtolower(trim($host)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function syncDomainHosts(Tenant $tenant, array $hosts): void
    {
        TenantDomain::query()->where('tenant_id', $tenant->getKey())->update([
            'is_active' => false,
            'is_primary' => false,
        ]);

        foreach (array_values($hosts) as $index => $host) {
            TenantDomain::query()->updateOrCreate(
                ['host' => $host],
                [
                    'tenant_id' => $tenant->getKey(),
                    'is_active' => true,
                    'is_primary' => $index === 0,
                ]
            );
        }
    }

    protected function syncTenantAdminContact(Tenant $tenant, string $email, ?string $name = null): void
    {
        $this->tenantDatabaseManager->connect($tenant);

        $admin = TenantAdmin::query()->orderBy('id')->first();

        if (! $admin) {
            throw ValidationException::withMessages([
                'admin_email' => 'No tenant coordinator account was found in the tenant database.',
            ]);
        }

        $emailTaken = TenantAdmin::query()
            ->where('email', $email)
            ->whereKeyNot($admin->getKey())
            ->exists()
            || Supervisor::query()->where('email', $email)->exists()
            || Student::query()->where('email', $email)->exists();

        if ($emailTaken) {
            throw ValidationException::withMessages([
                'admin_email' => 'This email is already used by another tenant account.',
            ]);
        }

        $admin->forceFill([
            'name' => filled($name) ? $name : $admin->name,
            'email' => $email,
        ])->save();
    }

    protected function sendActivationNotice(Tenant $tenant): bool
    {
        return $this->sendLifecycleMail($tenant, fn ($contact) => new TenantActivatedMail($tenant, $contact->name));
    }

    protected function sendSubscriptionUpdatedNotice(Tenant $tenant): bool
    {
        return $this->sendLifecycleMail($tenant, fn ($contact) => new TenantSubscriptionUpdatedMail($tenant, $contact->name));
    }

    protected function sendLifecycleMail(Tenant $tenant, callable $mailableResolver): bool
    {
        $this->tenantDatabaseManager->connect($tenant);

        $contacts = TenantAdmin::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['name', 'email']);

        if ($contacts->isEmpty()) {
            return false;
        }

        foreach ($contacts as $contact) {
            Mail::to($contact->email)->send($mailableResolver($contact));
        }

        return true;
    }

    protected function authorizeTenantManagement(): void
    {
        Gate::forUser(Auth::guard('central_superadmin')->user())->authorize('manage-tenants');
    }
}
