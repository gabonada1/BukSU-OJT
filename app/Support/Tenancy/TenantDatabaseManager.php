<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantDatabaseManager
{
    public function disconnect(): void
    {
        $connectionName = config('tenancy.tenant_connection', 'tenant');

        DB::disconnect($connectionName);
        DB::purge($connectionName);
    }

    public function connect(Tenant $tenant): void
    {
        $connectionName = config('tenancy.tenant_connection', 'tenant');
        $baseConfig = config("database.connections.{$connectionName}", []);

        Config::set("database.connections.{$connectionName}", array_filter([
            ...$baseConfig,
            'database' => $tenant->database,
            'host' => $tenant->db_host ?: ($baseConfig['host'] ?? null),
            'port' => $tenant->db_port ?: ($baseConfig['port'] ?? null),
            'username' => $tenant->db_username ?: ($baseConfig['username'] ?? null),
            'password' => $tenant->db_password ?: ($baseConfig['password'] ?? null),
        ], static fn ($value) => $value !== null));

        DB::purge($connectionName);
    }
}
