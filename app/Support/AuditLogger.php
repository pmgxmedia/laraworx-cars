<?php

namespace App\Support;

use App\Models\AuditLog;

class AuditLogger
{
    public static function log(
        string $action,
        ?string $entityType = null,
        int|string|null $entityId = null,
        ?string $description = null,
        ?array $before = null,
        ?array $after = null,
        ?int $userId = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $userId ?? auth()->id(),
            'entity_type' => $entityType,
            'entity_id' => $entityId !== null ? (int) $entityId : null,
            'action' => $action,
            'description' => $description,
            'before' => $before,
            'after' => $after,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
            'occurred_at' => now(),
        ]);
    }

    public static function auth(string $action, int $userId): AuditLog
    {
        return static::log($action, 'user', $userId, ucfirst(str_replace('_', ' ', $action)));
    }
}