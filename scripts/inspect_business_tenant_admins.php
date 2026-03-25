<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = App\Models\Tenant::query()->find(11);

if (! $tenant) {
    echo "tenant-not-found".PHP_EOL;
    exit(1);
}

app(App\Support\Tenancy\TenantDatabaseManager::class)->connect($tenant);

foreach (App\Models\TenantAdmin::query()->get(['id', 'name', 'email', 'is_active']) as $admin) {
    echo implode('|', [
        $admin->id,
        $admin->name,
        $admin->email,
        $admin->is_active ? '1' : '0',
    ]).PHP_EOL;
}
