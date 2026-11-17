<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    protected $fillable = [
        'full_name', 'username', 'email', 'phone', 'role',
        'office_id', 'password', 'must_change_password', 'is_active',
    ];

    // Never include the password when the user is turned into text or JSON
    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',          // plain text is scrambled automatically when saved
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
            'role' => UserRole::class,
        ];
    }

    // Each user belongs to one office
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function temporaryPasswords(): HasMany
    {
        return $this->hasMany(TemporaryPassword::class);
    }

        public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    // $user->initials  ->  "JD" for "Juan Dela Cruz" (first letters of the first two words)
    protected function initials(): Attribute
    {
        return Attribute::get(function () {
            $parts = preg_split('/\s+/', trim($this->full_name ?: $this->username), -1, PREG_SPLIT_NO_EMPTY);
            $first = mb_substr($parts[0] ?? '?', 0, 1);
            $second = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '';

            return mb_strtoupper($first . $second);
        });
    }
}