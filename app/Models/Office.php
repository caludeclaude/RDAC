<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    protected $fillable = ['name', 'office_type', 'province', 'city', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // One office has many users
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // One office currently holds many reports.
    // We name the column because it is not called office_id.
    public function currentReports(): HasMany
    {
        return $this->hasMany(Report::class, 'current_office_id');
    }
}