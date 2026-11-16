<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('SEED_ADMIN_PASSWORD');

        if (! $password) {
            throw new \RuntimeException('Please set SEED_ADMIN_PASSWORD in your .env file first.');
        }

        $office = Office::where('name', 'Regional Office')->firstOrFail();

        User::firstOrCreate(
            ['username' => env('SEED_ADMIN_USERNAME', 'admin')],
            [
                'full_name' => 'Regional Administrator',
                'email' => env('SEED_ADMIN_EMAIL'),
                'role' => UserRole::SuperAdmin,
                'office_id' => $office->id,
                'password' => $password,            // scrambled automatically by the model
                'must_change_password' => false,    // you chose this password yourself
                'is_active' => true,
            ]
        );
    }
}