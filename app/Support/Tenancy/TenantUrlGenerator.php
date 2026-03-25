<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantUrlGenerator
{
    public function usesLocalTenantDomains(): bool
    {
        $parts = parse_url((string) config('app.url'));
        $appHost = strtolower((string) ($parts['host'] ?? ''));
        $localSuffix = strtolower((string) config('tenancy.local_domain_suffix', 'localhost'));

        return in_array($appHost, ['localhost', '127.0.0.1', $localSuffix], true);
    }

    public function hostnameSuffix(): string
    {
        $parts = parse_url((string) config('app.url'));
        $appHost = strtolower((string) ($parts['host'] ?? ''));
        $localSuffix = strtolower((string) config('tenancy.local_domain_suffix', 'localhost'));

        if (in_array($appHost, ['localhost', '127.0.0.1', $localSuffix], true)) {
            return $localSuffix;
        }

        return (string) config('tenancy.base_domain', 'buksu.test');
    }

    public function subdomainHost(string $subdomain): string
    {
        return strtolower(trim($subdomain).'.'.$this->hostnameSuffix());
    }

    public function acronymCode(string $name): string
    {
        $words = collect(preg_split('/[^A-Za-z0-9]+/', strtoupper($name)) ?: [])
            ->filter();

        $code = $words->map(fn (string $word) => Str::substr($word, 0, 1))->implode('');

        if ($code !== '') {
            return Str::substr($code, 0, 6);
        }

        return strtoupper(Str::substr(Str::slug($name, ''), 0, 6));
    }

    public function localAliasHosts(?string $subdomain = null, ?string $tenantName = null, ?string $code = null): array
    {
        if (! $this->usesLocalTenantDomains()) {
            return [];
        }

        $hosts = collect();

        if (filled($subdomain)) {
            $hosts->push($this->subdomainHost((string) $subdomain));
        }

        $resolvedCode = filled($code) ? (string) $code : (filled($tenantName) ? $this->acronymCode((string) $tenantName) : null);

        if (filled($resolvedCode)) {
            $hosts->push($this->subdomainHost(Str::lower((string) $resolvedCode)));
        }

        return $hosts
            ->map(fn (string $host) => strtolower(trim($host)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function tenantHost(Tenant $tenant): ?string
    {
        $host = $tenant->primaryHost();

        if (! $this->usesLocalTenantDomains()) {
            return $host;
        }

        $preferredSuffix = '.'.$this->hostnameSuffix();
        $domains = $tenant->relationLoaded('domains')
            ? $tenant->domains
            : $tenant->domains()->get();

        $preferred = $domains
            ->where('is_active', true)
            ->sortByDesc('is_primary')
            ->pluck('host')
            ->map(fn (string $candidate) => strtolower(trim($candidate)))
            ->first(fn (string $candidate) => str_ends_with($candidate, $preferredSuffix));

        return $preferred ?: $host;
    }

    public function tenantBaseUrl(Tenant $tenant): string
    {
        $host = $this->tenantHost($tenant);

        if (! $host) {
            return rtrim((string) config('app.url'), '/');
        }

        $parts = parse_url((string) config('app.url'));
        $scheme = $parts['scheme'] ?? 'http';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return "{$scheme}://{$host}{$port}";
    }

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

        return rtrim($this->tenantBaseUrl($tenant), '/').$path;
    }
}
