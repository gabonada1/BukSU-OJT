<?php

use App\Support\Documentation\ProjectDocumentationWriter;
use App\Models\Tenant;
use App\Support\Tenancy\TenantDatabaseManager;
use App\Support\Tenancy\TenantSubscriptionNotifier;
use Database\Seeders\TenantDatabaseSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tenants:migrate {tenant?}', function (TenantDatabaseManager $databaseManager) {
    $slug = $this->argument('tenant');

    $tenants = Tenant::query()
        ->when($slug, fn ($query) => $query->where('slug', $slug))
        ->where('is_active', true)
        ->get();

    if ($tenants->isEmpty()) {
        $this->error('No active tenants found for migration.');

        return self::FAILURE;
    }

    foreach ($tenants as $tenant) {
        $databaseManager->connect($tenant);

        $this->components->info("Migrating tenant [{$tenant->slug}] using database [{$tenant->database}]");

        $this->call('migrate', [
            '--database' => config('tenancy.tenant_connection', 'tenant'),
            '--path' => 'database/migrations/tenant',
            '--realpath' => false,
            '--force' => true,
        ]);
    }

    return self::SUCCESS;
})->purpose('Run tenant migrations against each tenant database');

Artisan::command('tenants:seed {tenant?}', function (TenantDatabaseManager $databaseManager) {
    $slug = $this->argument('tenant');

    $tenants = Tenant::query()
        ->when($slug, fn ($query) => $query->where('slug', $slug))
        ->where('is_active', true)
        ->get();

    if ($tenants->isEmpty()) {
        $this->error('No active tenants found for seeding.');

        return self::FAILURE;
    }

    foreach ($tenants as $tenant) {
        $databaseManager->connect($tenant);

        $this->components->info("Seeding tenant [{$tenant->slug}]");

        $this->call('db:seed', [
            '--class' => TenantDatabaseSeeder::class,
            '--database' => config('tenancy.tenant_connection', 'tenant'),
            '--force' => true,
        ]);
    }

    return self::SUCCESS;
})->purpose('Seed tenant role accounts and starter data');

Artisan::command('docs:generate-project', function (ProjectDocumentationWriter $writer) {
    $path = $writer->write();

    $this->components->info("Project documentation generated at [{$path}]");

    return self::SUCCESS;
})->purpose('Generate the full project documentation markdown file');

Artisan::command('tenants:notify-subscriptions {--days=7}', function (TenantSubscriptionNotifier $notifier) {
    $days = max(1, (int) $this->option('days'));
    $warningCount = 0;
    $suspensionCount = 0;
    $errorCount = 0;

    foreach (Tenant::query()->orderBy('name')->get() as $tenant) {
        try {
            if (! $tenant->is_active) {
                if ($notifier->sendSuspensionNotice($tenant)) {
                    $suspensionCount++;
                }

                continue;
            }

            if (! $notifier->shouldWarnForExpiry($tenant, $days)) {
                continue;
            }

            if ($notifier->sendExpiryWarning($tenant, $notifier->daysRemaining($tenant))) {
                $warningCount++;
            }
        } catch (Throwable $exception) {
            $errorCount++;
            report($exception);
            $this->components->warn("Skipped [{$tenant->slug}] because notification delivery failed.");
        }
    }

    $this->components->info("Subscription warnings sent: {$warningCount}");
    $this->components->info("Suspension notices sent: {$suspensionCount}");
    $this->components->info("Notification errors: {$errorCount}");

    return self::SUCCESS;
})->purpose('Send tenant suspension notices and upcoming subscription expiry reminders');

Schedule::command('tenants:notify-subscriptions --days=7')->dailyAt('08:00');
