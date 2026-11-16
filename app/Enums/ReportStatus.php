<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case ForClosing = 'for_closing';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Under Review',
            self::Approved => 'Approved',
            self::ForClosing => 'For Closing',
            self::Resolved => 'Resolved',
        };
    }
}