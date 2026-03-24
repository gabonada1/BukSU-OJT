<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;

class TenantUrlGenerator
{
    public function loginUrl(Tenant $tenant): string
    {
        return $this->tenantUrl($tenant, '/login');
    }

    public function registerUrl(Tenant $tenant): string
    {
        return $this->tenantUrl($tenant, '/register');
    }

    public function verificationUrl(Tenant $tenant, string $token): string
    {
        return $this->tenantUrl($tenant, '/register/verify/'.$token);
    }

    public function centralLoginUrl(): string
    {
        return rtrim(config('app.url'), '/').'/central/login';
    }

    protected function tenantUrl(Tenant $tenant, string $path): string
    {
        $path = '/'.ltrim($path, '/');
        $appUrl = rtrim((string) config('app.url'), '/');

        if (filled($tenant->domain)) {
            $parsed = parse_url($appUrl);
            $scheme = $parsed['scheme'] ?? 'http';
            $port = isset($parsed['port']) ? ':'.$parsed['port'] : '';

            return "{$scheme}://{$tenant->domain}{$port}{$path}";
        }

        return "{$appUrl}/tenants/{$tenant->slug}{$path}";
    }
}
