<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Penro = 'penro';
    case Cenro = 'cenro';
    case Division = 'division';

    // The nice text shown on screen
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Penro => 'PENRO',
            self::Cenro => 'CENRO',
            self::Division => 'Division',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }
}