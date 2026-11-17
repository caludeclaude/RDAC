<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    // May this person see this complaint?
    // (the single visibility rule that your old canUserSeeReport_ function held)
    public function view(User $user, Report $report): bool
    {
        // The Super Admin sees everything
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Archived complaints are hidden from everyone else
        if ($report->archived_at !== null) {
            return false;
        }

        // Others see a complaint their office first received, has held, or that they filed
        return $report->filed_by_user_id === $user->id
            || $report->initial_office_id === $user->office_id
            || $report->routings()->where('office_id', $user->office_id)->exists();
    }
}