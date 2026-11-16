<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\User;
use Throwable;

class Audit
{
    // Writes one line to the audit_logs table.
    // It never throws an error: a logging problem must not break the action being logged.
    public static function record(string $activity, ?User $user = null, ?string $username = null): void
    {
        try {
            AuditLog::create([
                'logged_at' => now(),
                'user_id' => $user?->id,
                'username' => mb_substr($user?->username ?? $username ?? 'SYSTEM', 0, 64),
                'activity' => $activity,
            ]);
        } catch (Throwable $e) {
            report($e);   // writes the problem to storage/logs/laravel.log
        }
    }
}