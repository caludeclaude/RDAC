<?php

namespace Database\Seeders;

use App\Models\ComplaintType;
use Illuminate\Database\Seeder;

class ComplaintTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Environmental Complaint',
            'Land / Forestry Issue',
            'Mining / Quarrying',
            'Wildlife Protection',
            'Public Inquiry',
        ];

        foreach ($types as $name) {
            ComplaintType::firstOrCreate(['name' => $name]);
        }
    }
}