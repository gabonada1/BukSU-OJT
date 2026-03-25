<?php

namespace App\Support\Security;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    public static function log(
        string $actorType,
        int|string|null $actorId,
        ?string $actorName,
        string $action,
        mixed $subject = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null
    ): void {
        Log::info('tenant_audit', [
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'action' => $action,
            'subject_type' => $subject instanceof Model ? $subject::class : null,
            'subject_id' => $subject instanceof Model ? $subject->getKey() : null,
            'subject_label' => method_exists($subject, 'getAuditLabel') ? $subject->getAuditLabel() : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
        ]);
    }
}
