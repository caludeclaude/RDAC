<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        Office::firstOrCreate(
            ['name' => 'Regional Office'],        // look for this
            ['office_type' => 'regional']         // if not found, create it with this
        );
    }
}