<?php

namespace App\Support\Tenancy;

use App\Mail\TenantSubscriptionExpiringMail;
use App\Mail\TenantSuspendedMail;
use App\Models\Tenant;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class TenantSubscriptionNotifier
{
    public function __construct(
        protected TenantAdminContactResolver $contactResolver,
    ) {
    }

    public function sendSuspensionNotice(Tenant $tenant, bool $force = false): bool
    {
        $settings = $tenant->settings ?? [];
        $alreadySent = Arr::get($settings, 'notifications.suspension_notice_sent_at');

        if ($alreadySent && ! $force) {
            return false;
        }

        $contacts = $this->contactResolver->contacts($tenant);

        if ($contacts->isEmpty()) {
            return false;
        }

        foreach ($contacts as $contact) {
            Mail::to($contact->email)->send(new TenantSuspendedMail($tenant, $contact->name));
        }

        $settings = $tenant->settings ?? [];
        Arr::set($settings, 'notifications.suspension_notice_sent_at', now()->toDateTimeString());
        $tenant->forceFill(['settings' => $settings])->save();

        return true;
    }

    public function sendExpiryWarning(Tenant $tenant, int $daysRemaining): bool
    {
        $expiryDate = $tenant->subscription_expires_at?->toDateString();

        if (! $expiryDate) {
            return false;
        }

        $settings = $tenant->settings ?? [];
        $sentMap = Arr::get($settings, 'notifications.expiry_notice_sent_for', []);

        if (array_key_exists($expiryDate, $sentMap)) {
            return false;
        }

        $contacts = $this->contactResolver->contacts($tenant);

        if ($contacts->isEmpty()) {
            return false;
        }

        foreach ($contacts as $contact) {
            Mail::to($contact->email)->send(
                new TenantSubscriptionExpiringMail($tenant, $contact->name, $daysRemaining)
            );
        }

        $sentMap[$expiryDate] = now()->toDateTimeString();
        Arr::set($settings, 'notifications.expiry_notice_sent_for', $sentMap);
        $tenant->forceFill(['settings' => $settings])->save();

        return true;
    }

    public function clearSuspensionNoticeFlag(Tenant $tenant): void
    {
        $settings = $tenant->settings ?? [];
        Arr::forget($settings, 'notifications.suspension_notice_sent_at');
        $tenant->forceFill(['settings' => $settings])->save();
    }

    public function shouldWarnForExpiry(Tenant $tenant, int $days = 7): bool
    {
        if (! $tenant->subscription_expires_at instanceof Carbon) {
            return false;
        }

        if (! $tenant->is_active) {
            return false;
        }

        $today = now()->startOfDay();
        $expiry = $tenant->subscription_expires_at->startOfDay();

        return $expiry->gte($today) && $expiry->lte($today->copy()->addDays($days));
    }

    public function daysRemaining(Tenant $tenant): int
    {
        return max(0, now()->startOfDay()->diffInDays($tenant->subscription_expires_at->startOfDay(), false));
    }
}
