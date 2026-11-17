<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class Navigation
{
    // Returns the menu items this user may see
    public static function for(User $user): array
    {
        $items = [
            ['icon' => 'fa-gauge-high',    'label' => 'Dashboard',       'route' => 'dashboard',        'match' => 'dashboard'],
            ['icon' => 'fa-list-check',    'label' => 'Complaints',      'route' => 'complaints.index', 'match' => 'complaints.*'],
            ['icon' => 'fa-circle-check',  'label' => 'Resolved',        'route' => 'resolved.index',   'match' => 'resolved.*'],
            ['icon' => 'fa-pen-to-square', 'label' => 'File Complaint',  'route' => 'reports.create',   'match' => 'reports.*'],

            ['separator' => true, 'only' => [UserRole::SuperAdmin]],

            ['icon' => 'fa-users-gear',    'label' => 'User Management', 'route' => 'users.index',      'match' => 'users.*',   'only' => [UserRole::SuperAdmin]],
            ['icon' => 'fa-box-archive',   'label' => 'Archive',         'route' => 'archive.index',    'match' => 'archive.*', 'only' => [UserRole::SuperAdmin]],
        ];

        $visible = [];

        foreach ($items as $item) {
            // 1. Skip items meant for other roles
            if (isset($item['only']) && ! in_array($user->role, $item['only'], true)) {
                continue;
            }

            // 2. Skip items whose page does not exist yet.
            //    As we build each page later, its menu item appears by itself.
            if (isset($item['route']) && ! Route::has($item['route'])) {
                continue;
            }

            $visible[] = $item;
        }

        // 3. Remove a separator that has nothing after it (or comes first)
        while ($visible && ! empty($visible[array_key_last($visible)]['separator'])) {
            array_pop($visible);
        }
        if ($visible && ! empty($visible[0]['separator'])) {
            array_shift($visible);
        }

        return $visible;
    }
}