<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function targetHost(string $host): string
{
    $host = strtolower(trim($host));

    if (str_ends_with($host, '.buksu.test')) {
        return (string) preg_replace('/\.buksu\.test$/', '.lvh.me', $host);
    }

    if (str_ends_with($host, '.localhost')) {
        return (string) preg_replace('/\.localhost$/', '.lvh.me', $host);
    }

    if ($host !== '' && ! str_contains($host, '.')) {
        return $host.'.lvh.me';
    }

    return $host;
}

$domains = App\Models\TenantDomain::query()
    ->orderBy('tenant_id')
    ->orderByDesc('is_primary')
    ->orderByDesc('is_active')
    ->orderBy('id')
    ->get();

$domains
    ->groupBy(fn ($domain) => $domain->tenant_id.'|'.targetHost((string) $domain->host))
    ->each(function ($group) {
        $winner = $group->first();
        $target = targetHost((string) $winner->host);

        if ($winner->host !== $target) {
            $winner->host = $target;
            $winner->save();
        }

        $group->slice(1)->each(function ($duplicate) use ($winner) {
            if ($duplicate->is_primary && ! $winner->is_primary) {
                $winner->is_primary = true;
            }

            if ($duplicate->is_active && ! $winner->is_active) {
                $winner->is_active = true;
            }

            $duplicate->delete();
        });

        $winner->save();
    });

foreach (App\Models\TenantDomain::query()->orderBy('tenant_id')->orderByDesc('is_primary')->get(['tenant_id', 'host', 'is_primary', 'is_active']) as $domain) {
    echo implode('|', [
        $domain->tenant_id,
        $domain->host,
        $domain->is_primary ? '1' : '0',
        $domain->is_active ? '1' : '0',
    ]).PHP_EOL;
}
