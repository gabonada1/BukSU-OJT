<?php

namespace App\Support\Billing;

class PlanCatalog
{
    public static function all(): array
    {
        return [
            'basic' => [
                'key' => 'basic',
                'label' => 'Basic',
                'amount' => self::amountFromEnv('PLAN_BASIC_AMOUNT', 29900),
                'currency' => self::currencyFromEnv(),
                'stripe_price_id' => self::stripePriceIdFromEnv('STRIPE_PRICE_BASIC'),
                'summary' => 'Launch a university portal with the core practicum workflow and role dashboards.',
                'features' => [
                    'Separate tenant database for one university portal',
                    'Partner companies, student applications, and OJT hour tracking',
                    'Student requirements, reports, and supervisor evaluations',
                ],
            ],
            'pro' => [
                'key' => 'pro',
                'label' => 'Pro',
                'amount' => self::amountFromEnv('PLAN_PRO_AMOUNT', 59900),
                'currency' => self::currencyFromEnv(),
                'stripe_price_id' => self::stripePriceIdFromEnv('STRIPE_PRICE_PRO'),
                'summary' => 'Adds stronger university customization and richer practicum coordination tools.',
                'features' => [
                    'Everything in Basic',
                    'Course-based OJT hour settings and coordinator controls',
                    'Branding controls and improved reporting workflows',
                ],
            ],
            'premium' => [
                'key' => 'premium',
                'label' => 'Premium',
                'amount' => self::amountFromEnv('PLAN_PREMIUM_AMOUNT', 99900),
                'currency' => self::currencyFromEnv(),
                'stripe_price_id' => self::stripePriceIdFromEnv('STRIPE_PRICE_PREMIUM'),
                'summary' => 'Best for universities that want the full University Practicum experience and central visibility.',
                'features' => [
                    'Everything in Pro',
                    'Advanced oversight for coordinators and central administration',
                    'Priority provisioning for new tenant rollouts and support',
                ],
            ],
        ];
    }

    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    protected static function currencyFromEnv(): string
    {
        return strtolower((string) env('BILLING_CURRENCY', 'PHP'));
    }

    protected static function amountFromEnv(string $key, int $fallback): int
    {
        $value = trim((string) env($key, ''));

        if ($value === '' || ! is_numeric($value)) {
            return $fallback;
        }

        return (int) round(((float) $value) * 100);
    }

    protected static function stripePriceIdFromEnv(string $key): ?string
    {
        $value = trim((string) env($key, ''));

        return $value !== '' ? $value : null;
    }
}
