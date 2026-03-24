<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Tenant extends Model
{
    protected $connection = 'central';

    protected $fillable = [
        'name',
        'slug',
        'code',
        'domain',
        'subdomain',
        'plan',
        'subscription_starts_at',
        'subscription_expires_at',
        'database',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => 'array',
            'subscription_starts_at' => 'date',
            'subscription_expires_at' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function matchesDomain(?string $host): bool
    {
        return $this->matchesHost($host);
    }

    public function matchesHost(?string $host): bool
    {
        $host = (string) $host;

        if (filled($this->domain) && strcasecmp($this->domain, $host) === 0) {
            return true;
        }

        return filled($this->subdomain)
            && strcasecmp($this->subdomain.'.'.config('tenancy.local_domain_suffix', 'localhost'), $host) === 0;
    }

    public function subscriptionHasStarted(): bool
    {
        return ! $this->subscription_starts_at instanceof Carbon
            || $this->subscription_starts_at->startOfDay()->lte(now()->startOfDay());
    }

    public function subscriptionIsExpired(): bool
    {
        return $this->subscription_expires_at instanceof Carbon
            && $this->subscription_expires_at->endOfDay()->isPast();
    }

    public function canAccessTenantApp(): bool
    {
        return $this->is_active
            && $this->subscriptionHasStarted()
            && ! $this->subscriptionIsExpired();
    }

    public function subscriptionStatus(): string
    {
        if (! $this->is_active) {
            return 'suspended';
        }

        if (! $this->subscriptionHasStarted()) {
            return 'scheduled';
        }

        if ($this->subscriptionIsExpired()) {
            return 'expired';
        }

        return 'active';
    }

    public function subscriptionBlockMessage(): string
    {
        return match ($this->subscriptionStatus()) {
            'suspended' => 'This tenant subscription is suspended.',
            'scheduled' => 'This tenant subscription has not started yet.',
            'expired' => 'This tenant subscription has expired.',
            default => 'This tenant is currently unavailable.',
        };
    }
}
