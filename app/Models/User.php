<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enums\UserRole;

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
}