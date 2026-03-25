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

$admin = App\Models\TenantAdmin::query()->orderBy('id')->first();

if (! $admin) {
    echo "admin-not-found".PHP_EOL;
    exit(1);
}

$temporaryPassword = 'P6GMJtdaZhbUE!';

$admin->forceFill([
    'password' => $temporaryPassword,
    'is_active' => true,
    'suspended_at' => null,
])->save();

echo implode('|', [
    $tenant->name,
    $admin->email,
    $temporaryPassword,
]).PHP_EOL;
