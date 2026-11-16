<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters: the admin needs the office to exist first
        $this->call([
            OfficeSeeder::class,
            ComplaintTypeSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}