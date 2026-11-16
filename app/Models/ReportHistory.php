<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ReportStatus;

class ReportHistory extends Model
{
    const UPDATED_AT = null;   // history rows are never edited, so only created_at exists

    protected $fillable = [
        'report_id', 'occurred_at', 'status', 'office_id', 'remarks',
        'acted_by_user_id', 'acted_by_office_id',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'status' => ReportStatus::class,
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function actedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acted_by_user_id');
    }

    public function actedByOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'acted_by_office_id');
    }
}