<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public static function log(string $action, ?string $entityType = null, ?int $entityId = null, array $metadata = []): void
    {
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata,
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
