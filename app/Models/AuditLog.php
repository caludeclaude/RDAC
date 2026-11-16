<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;    // we use our own logged_at column instead

    protected $fillable = ['logged_at', 'user_id', 'username', 'activity'];

    protected function casts(): array
    {
        return ['logged_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}