<?php

namespace App\Http\Controllers\Central;

use App\Mail\TenantAdminCredentialsMail;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantAdmin;
use App\Support\Security\PasswordGenerator;
use App\Support\Tenancy\TenantDatabaseManager;
use App\Support\Tenancy\TenantSubscriptionNotifier;
use App\Support\Tenancy\TenantUrlGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class TenantProvisionController extends Controller
{
    public function __construct(
        protected TenantDatabaseManager $tenantDatabaseManager,
        protected PasswordGenerator $passwordGenerator,
        protected TenantSubscriptionNotifier $subscriptionNotifier,
        protected TenantUrlGenerator $urlGenerator,
    ) {
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plan' => ['required', Rule::in(['basic', 'pro', 'premium'])],
            'subscription_starts_at' => ['required', 'date'],
            'subscription_expires_at' => ['nullable', 'date', 'after_or_equal:subscription_starts_at'],
            'subdomain' => ['required', 'alpha_dash', 'max:63', Rule::unique('central.tenants', 'subdomain')],
            'database' => ['required', 'regex:/^[A-Za-z0-9_]+$/', Rule::unique('central.tenants', 'database')],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ]);

        $slug = Str::slug($validated['name']);
        $domain = $validated['subdomain'].'.'.config('tenancy.base_domain');
        $tenantCode = Str::upper(Str::substr(Str::slug($validated['name'], ''), 0, 6));
        $adminName = $validated['name'].' Admin';
        $adminPassword = filled($validated['admin_password'] ?? null)
            ? $validated['admin_password']
            : $this->passwordGenerator->generate();

        $tenant = null;

        try {
            $tenant = Tenant::query()->create([
                'name' => $validated['name'],
                'slug' => $this->uniqueSlug($slug),
                'code' => $tenantCode,
                'plan' => $validated['plan'],
                'subscription_starts_at' => $validated['subscription_starts_at'],
                'subscription_expires_at' => $validated['subscription_expires_at'] ?? null,
                'domain' => $domain,
                'subdomain' => $validated['subdomain'],
                'database' => $validated['database'],
                'db_host' => env('TENANT_DB_HOST', '127.0.0.1'),
                'db_port' => env('TENANT_DB_PORT', '3306'),
                'db_username' => env('TENANT_DB_USERNAME', 'root'),
                'db_password' => env('TENANT_DB_PASSWORD', ''),
                'is_active' => true,
                'settings' => [
                    'provisioned_by' => 'central_superadmin',
                    'provision_defaults' => [
                        'generated_code' => $tenantCode,
                        'generated_admin_name' => $adminName,
                    ],
                    'branding' => [
                        'accent' => '#dc2626',
                        'secondary' => '#ffffff',
                    ],
                ],
            ]);

            $this->createTenantDatabase($tenant->database);
            $this->tenantDatabaseManager->connect($tenant);

            Artisan::call('migrate', [
                '--database' => config('tenancy.tenant_connection', 'tenant'),
                '--path' => 'database/migrations/tenant',
                '--realpath' => false,
                '--force' => true,
            ]);

            TenantAdmin::query()->create([
                'name' => $adminName,
                'email' => $validated['admin_email'],
                'password' => $adminPassword,
                'is_active' => true,
            ]);

            rescue(function () use ($tenant, $adminName, $validated, $adminPassword) {
                Mail::to($validated['admin_email'])->send(
                    new TenantAdminCredentialsMail(
                        $tenant,
                        $adminName,
                        $validated['admin_email'],
                        $adminPassword,
                        $this->urlGenerator,
                    )
                );
            }, report: true);
        } catch (Throwable $exception) {
            if ($tenant?->exists) {
                rescue(fn () => $tenant->delete(), report: false);
            }

            report($exception);

            return back()
                ->withInput($request->except('admin_password'))
                ->withErrors([
                    'provisioning' => 'Tenant provisioning failed. Check that MySQL is running and that your central and tenant database settings in `.env` are correct.',
                ]);
        }

        return redirect()->route('central.dashboard')->with('status', "Tenant {$tenant->name} created successfully.");
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $wasActive = $tenant->is_active;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plan' => ['required', Rule::in(['basic', 'pro', 'premium'])],
            'subscription_starts_at' => ['required', 'date'],
            'subscription_expires_at' => ['nullable', 'date', 'after_or_equal:subscription_starts_at'],
            'is_active' => ['required', Rule::in(['0', '1'])],
        ]);

        $tenant->update([
            'name' => $validated['name'],
            'plan' => $validated['plan'],
            'subscription_starts_at' => $validated['subscription_starts_at'],
            'subscription_expires_at' => $validated['subscription_expires_at'] ?? null,
            'is_active' => (bool) $validated['is_active'],
        ]);

        if (! $tenant->is_active) {
            rescue(fn () => $this->subscriptionNotifier->sendSuspensionNotice($tenant), report: true);
        } elseif (! $wasActive && $tenant->is_active) {
            rescue(fn () => $this->subscriptionNotifier->clearSuspensionNoticeFlag($tenant), report: true);
        }

        if ($this->subscriptionNotifier->shouldWarnForExpiry($tenant)) {
            rescue(function () use ($tenant) {
                $this->subscriptionNotifier->sendExpiryWarning(
                    $tenant,
                    $this->subscriptionNotifier->daysRemaining($tenant)
                );
            }, report: true);
        }

        return redirect()
            ->route('central.dashboard', ['section' => 'directory'])
            ->with('status', "Tenant {$tenant->name} updated successfully.");
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
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
                    'provisioning' => "Tenant deletion failed for {$tenantName}. Check that MySQL is running and that the database user can drop tenant databases.",
                ]);
        }

        return redirect()
            ->route('central.dashboard', ['section' => 'directory'])
            ->with('status', "Tenant {$tenantName} and database {$databaseName} deleted successfully.");
    }

    protected function createTenantDatabase(string $database): void
    {
        $databaseName = str_replace('`', '', $database);

        DB::connection(config('tenancy.central_connection', 'central'))
            ->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    protected function dropTenantDatabase(string $database): void
    {
        $databaseName = str_replace('`', '', $database);

        DB::connection(config('tenancy.central_connection', 'central'))
            ->statement("DROP DATABASE IF EXISTS `{$databaseName}`");
    }

    protected function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 2;

        while (Tenant::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
