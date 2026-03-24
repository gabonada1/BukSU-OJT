<?php

namespace App\Support\Documentation;

use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ProjectDocumentationWriter
{
    protected ?string $warning = null;

    public function write(): string
    {
        $path = base_path('docs/PROJECT_DOCUMENTATION.md');

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $this->content());

        return $path;
    }

    protected function content(): string
    {
        $tenants = $this->tenants();

        return implode("\n", [
            '# BukSU Internship / Practicum Placement System',
            '',
            '> Auto-generated project documentation. This file is refreshed by `php artisan docs:generate-project` and automatically updates when tenant records are created, updated, or deleted through the application.',
            '',
            '## 1. Project Summary',
            '',
            'The BukSU Internship / Practicum Placement System is a multitenant Laravel application that separates a **central application** from the **tenant application**.',
            '',
            '- The **central application** runs with no tenant context and is used by the BukSU superadmin to manage tenants, plans, domains, and provisioning.',
            '- The **tenant application** runs in tenant context and is used by each college for practicum operations, role-based access, and tenant-specific records.',
            ...$this->warningSection(),
            '',
            '## 2. Architecture',
            '',
            '### Central Application',
            '',
            '- Uses the central database: `'.config('database.default').'` with central connection `'.config('tenancy.central_connection').'`.',
            '- Main responsibilities:',
            '- Authenticate the central superadmin.',
            '- Create and register tenant colleges.',
            '- Generate and assign subdomains/domains.',
            '- Create tenant databases and launch tenant migrations.',
            '- Maintain the tenant directory and launch links.',
            '',
            '### Tenant Application',
            '',
            '- Uses the tenant connection `'.config('tenancy.tenant_connection').'` after tenant resolution.',
            '- Main responsibilities:',
            '- Authenticate tenant admins, supervisors, and students.',
            '- Manage partner companies, students, requirements, and OJT hour logs.',
            '- Render tenant dashboards for each role.',
            '',
            '## 3. Databases',
            '',
            '- Central database connection: `'.config('tenancy.central_connection').'`.',
            '- Tenant database connection: `'.config('tenancy.tenant_connection').'`.',
            '- Base domain for generated tenant subdomains: `'.config('tenancy.base_domain').'`.',
            '- Central domains: `'.implode('`, `', config('tenancy.central_domains', [])).'`.',
            '',
            '### Central Database Stores',
            '',
            '- Tenant registry and metadata',
            '- Central superadmin accounts',
            '- Shared app-level configuration',
            '',
            '### Tenant Database Stores',
            '',
            '- Tenant admins',
            '- Supervisors',
            '- Students',
            '- Partner companies',
            '- Requirements',
            '- OJT hour logs',
            '',
            '## 4. Authentication and Roles',
            '',
            '### Central Role',
            '',
            '- Superadmin',
            '- Login: `/central/login` on a central domain such as `127.0.0.1` or `localhost`.',
            '',
            '### Tenant Roles',
            '',
            '- Tenant Admin: manages companies, students, supervisors, requirements, and OJT tracking.',
            '- Supervisor: views assigned students and practicum activity.',
            '- Student: views placement, requirement status, and OJT progress.',
            '- Shared tenant login: `/tenants/{tenant}/login` or `/login` on a tenant domain.',
            '',
            '## 5. Route Structure',
            '',
            '- `routes/web.php`: top-level entry resolver.',
            '- `routes/central.php`: central application routes.',
            '- `routes/tenant.php`: tenant application routes.',
            '',
            '### Important Central Routes',
            '',
            '- `GET /` -> central app entry resolver',
            '- `GET /central/login` -> superadmin login page',
            '- `GET /central/dashboard` -> superadmin dashboard',
            '- `POST /central/tenants` -> create a new tenant and subdomain',
            '',
            '### Important Tenant Routes',
            '',
            '- `GET /tenants/{tenant}` -> tenant app entry',
            '- `GET /tenants/{tenant}/login` -> tenant login page',
            '- `GET /tenants/{tenant}/admin/dashboard` -> tenant admin dashboard',
            '- `GET /tenants/{tenant}/supervisor/dashboard` -> supervisor dashboard',
            '- `GET /tenants/{tenant}/student/dashboard` -> student dashboard',
            '- `GET /login` on a tenant domain -> tenant login page',
            '',
            '## 6. Provisioning Flow',
            '',
            'When the superadmin creates a new tenant from the central dashboard, the application:',
            '',
            '1. Saves the tenant metadata in the central database.',
            '2. Generates the tenant domain from `subdomain + base domain`.',
            '3. Creates the tenant database if it does not yet exist.',
            '4. Runs tenant migrations on the new database.',
            '5. Creates the first tenant admin account.',
            '6. Refreshes this project documentation file automatically.',
            '',
            '## 7. Current Managed Tenants',
            '',
            $this->tenantTable($tenants),
            '',
            '## 8. Seeded Local Credentials',
            '',
            '### Central Superadmin',
            '',
            '- Email: `'.env('CENTRAL_SUPERADMIN_EMAIL', 'superadmin@buksu.test').'`',
            '- Password: defined by `CENTRAL_SUPERADMIN_PASSWORD` in `.env`',
            '',
            '### Default Tenant Demo Accounts',
            '',
            '- Admin: `admin@technology.buksu.test` / `password123`',
            '- Supervisor: `supervisor@technology.buksu.test` / `password123`',
            '- Student: `student@technology.buksu.test` / `password123`',
            '',
            '## 9. Local Development',
            '',
            '### XAMPP Workflow',
            '',
            '- Start Apache and MySQL in XAMPP.',
            '- Open the central app at `http://localhost/buksu-practicum/central/login`.',
            '- Open tenant apps with `http://localhost/buksu-practicum/tenants/{tenant}/login`.',
            '- Run `npm run build` only when frontend assets change.',
            '',
            '### Maintenance Commands',
            '',
            '```bash',
            'php artisan migrate',
            'php artisan db:seed',
            'php artisan tenants:migrate college-of-technologies',
            'php artisan tenants:seed college-of-technologies',
            'php artisan docs:generate-project',
            'npm run build',
            '```',
            '',
            '## 10. Documentation Automation',
            '',
            '- This file is generated at: `docs/PROJECT_DOCUMENTATION.md`.',
            '- Manual refresh command: `php artisan docs:generate-project`.',
            '- Automatic refresh happens whenever a tenant record is saved or deleted through the application.',
            '',
            '## 11. Key Files',
            '',
            '- Central dashboard controller: `app/Http/Controllers/Central/CentralDashboardController.php`',
            '- Central provisioning controller: `app/Http/Controllers/Central/TenantProvisionController.php`',
            '- Central auth controller: `app/Http/Controllers/Central/CentralAuthController.php`',
            '- Tenant auth controller: `app/Http/Controllers/TenantAuthController.php`',
            '- Tenancy config: `config/tenancy.php`',
            '- Central routes: `routes/central.php`',
            '- Tenant routes: `routes/tenant.php`',
            '',
        ]);
    }

    protected function tenants(): Collection
    {
        try {
            if (! Schema::connection(config('tenancy.central_connection', 'central'))->hasTable('tenants')) {
                return collect();
            }

            return Tenant::query()
                ->orderBy('name')
                ->get();
        } catch (Throwable $exception) {
            $this->warning = 'The central database was unavailable while generating this documentation. Static architecture content was written, but the live tenant directory could not be refreshed.';

            return collect();
        }
    }

    protected function tenantTable(Collection $tenants): string
    {
        if ($tenants->isEmpty()) {
            return 'No tenants are currently registered.';
        }

        $rows = [
            '| Name | Slug | Plan | Subdomain | Domain | Database | Status |',
            '| --- | --- | --- | --- | --- | --- | --- |',
        ];

        foreach ($tenants as $tenant) {
            $rows[] = sprintf(
                '| %s | %s | %s | %s | %s | %s | %s |',
                $tenant->name,
                $tenant->slug,
                strtoupper($tenant->plan),
                $tenant->subdomain ?: 'n/a',
                $tenant->domain ?: 'n/a',
                $tenant->database,
                $tenant->is_active ? 'Active' : 'Inactive',
            );
        }

        return implode("\n", $rows);
    }

    protected function warningSection(): array
    {
        if (! $this->warning) {
            return [];
        }

        return [
            '',
            '> Warning: '.$this->warning,
        ];
    }
}
