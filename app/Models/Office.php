<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Office extends Model
{
    public const PROVINCES = ['Batangas', 'Cavite', 'Laguna', 'Quezon', 'Rizal'];

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

    // Offices a complaint may be sent to: active ones that have at least one
    // active account, plus the Regional Office.   Used as:  Office::routable()->get()
    public function scopeRoutable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q) {
                $q->where('office_type', 'regional')
                  ->orWhereHas('users', fn (Builder $u) => $u->where('is_active', true));
            });
    }
}