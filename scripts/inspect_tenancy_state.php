<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "[tenants]".PHP_EOL;
foreach (App\Models\Tenant::query()->orderBy('id')->get(['id', 'name', 'code', 'database', 'is_active']) as $tenant) {
    echo implode('|', [
        $tenant->id,
        $tenant->name,
        $tenant->code,
        $tenant->database,
        $tenant->is_active ? '1' : '0',
    ]).PHP_EOL;
}

echo PHP_EOL."[domains]".PHP_EOL;
foreach (App\Models\TenantDomain::query()->orderBy('tenant_id')->orderByDesc('is_primary')->get(['tenant_id', 'host', 'is_primary', 'is_active']) as $domain) {
    echo implode('|', [
        $domain->tenant_id,
        $domain->host,
        $domain->is_primary ? '1' : '0',
        $domain->is_active ? '1' : '0',
    ]).PHP_EOL;
}

echo PHP_EOL."[databases]".PHP_EOL;
$rows = Illuminate\Support\Facades\DB::connection(config('tenancy.central_connection', 'central'))->select('SHOW DATABASES');
foreach ($rows as $row) {
    $values = array_values((array) $row);
    echo $values[0].PHP_EOL;
}
