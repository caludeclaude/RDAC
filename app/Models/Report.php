<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ReportStatus;

class Report extends Model
{
    protected $fillable = [
        'report_number', 'subject', 'complaint_type_id', 'description',
        'filed_at', 'filed_by_user_id',
        'is_anonymous', 'complainant_name', 'complainant_phone', 'complainant_email',
        'barangay', 'city', 'province',
        'status', 'initial_office_id', 'current_office_id', 'resolved_at',
        'archived_at', 'archived_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'filed_at' => 'datetime',
            'resolved_at' => 'datetime',
            'archived_at' => 'datetime',
            'is_anonymous' => 'boolean',
            'status' => ReportStatus::class,
        ];
    }

    // complaint_type_id -> Laravel guesses this one from the method name
    public function complaintType(): BelongsTo
    {
        return $this->belongsTo(ComplaintType::class);
    }

    // These names differ from the table, so we say which column to use
    public function filedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filed_by_user_id');
    }

    public function initialOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'initial_office_id');
    }

    public function currentOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'current_office_id');
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by_user_id');
    }

    // One report has many history rows, oldest first
    public function histories(): HasMany
    {
        return $this->hasMany(ReportHistory::class)->orderBy('occurred_at')->orderBy('id');
    }

    // The chain of offices, in order
    public function routings(): HasMany
    {
        return $this->hasMany(ReportRouting::class)->orderBy('position');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ReportAttachment::class);
    }

    // Addresses use the report number: /reports/RDAC-2026-00001/attachment
    public function getRouteKeyName(): string
    {
        return 'report_number';
    }
}