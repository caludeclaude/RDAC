<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportRouting extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['report_id', 'position', 'office_id', 'routed_by_user_id', 'routed_at'];

    protected function casts(): array
    {
        return ['routed_at' => 'datetime'];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function routedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'routed_by_user_id');
    }
}