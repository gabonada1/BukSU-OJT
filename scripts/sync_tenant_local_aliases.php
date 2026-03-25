<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$urlGenerator = app(App\Support\Tenancy\TenantUrlGenerator::class);

$normalizeHost = static function (string $host): string {
    $host = strtolower(trim($host));

    if ($host === '') {
        return $host;
    }

    if (str_ends_with($host, '.buksu.test')) {
        return (string) preg_replace('/\.buksu\.test$/', '.lvh.me', $host);
    }

    if (str_ends_with($host, '.localhost')) {
        return (string) preg_replace('/\.localhost$/', '.lvh.me', $host);
    }

    if (! str_contains($host, '.')) {
        return $host.'.lvh.me';
    }

    return $host;
};

App\Models\Tenant::query()
    ->with('domains')
    ->orderBy('id')
    ->get()
    ->each(function (App\Models\Tenant $tenant) use ($urlGenerator, $normalizeHost): void {
        $primaryHost = $tenant->primaryHost();
        $subdomain = null;

        if ($primaryHost) {
            $parts = explode('.', $normalizeHost($primaryHost));
            $subdomain = $parts[0] ?? null;
        }

        $hosts = collect(array_merge(
            $tenant->domains->pluck('host')->map($normalizeHost)->all(),
            $urlGenerator->localAliasHosts($subdomain, $tenant->name, $tenant->code)
        ))
            ->map(fn (string $host) => strtolower(trim($host)))
            ->filter()
            ->unique()
            ->values();

        $preferredPrimary = collect($urlGenerator->localAliasHosts($subdomain, $tenant->name, $tenant->code))->first();
        $primary = strtolower((string) ($preferredPrimary ?: ($primaryHost ? $normalizeHost($primaryHost) : $hosts->first())));

        foreach ($hosts as $host) {
            App\Models\TenantDomain::query()->updateOrCreate(
                ['host' => $host],
                [
                    'tenant_id' => $tenant->getKey(),
                    'is_primary' => $host === $primary,
                    'is_active' => true,
                ]
            );
        }

        App\Models\TenantDomain::query()
            ->where('tenant_id', $tenant->getKey())
            ->whereNotIn('host', $hosts->all())
            ->update([
                'is_primary' => false,
                'is_active' => false,
            ]);
    });

App\Models\Tenant::query()
    ->with('domains')
    ->orderBy('id')
    ->get()
    ->each(function (App\Models\Tenant $tenant): void {
        echo $tenant->name.PHP_EOL;

        foreach ($tenant->domains()->orderByDesc('is_primary')->orderBy('host')->get(['host', 'is_primary', 'is_active']) as $domain) {
            echo '  - '.$domain->host.'|'.($domain->is_primary ? '1' : '0').'|'.($domain->is_active ? '1' : '0').PHP_EOL;
        }
    });
