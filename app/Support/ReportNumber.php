<?php

namespace App\Support;

use App\Models\ReportNumberSequence;
use Illuminate\Support\Facades\DB;

class ReportNumber
{
    // Returns the next number, for example "RDAC-2026-00001".
    // Call it INSIDE the transaction that saves the report, so that a failed
    // save also undoes the counter (no gaps in the numbering).
    public static function next(): string
    {
        $year = (int) now()->format('Y');   // the year in Philippine time

        return DB::transaction(function () use ($year) {

            // 1. Make sure this year has a row (does nothing if it already exists)
            DB::table('report_number_sequences')->insertOrIgnore([
                'year' => $year,
                'last_number' => 0,
                'updated_at' => now(),
            ]);

            // 2. Lock the row: anyone else asking at the same moment waits here
            $sequence = ReportNumberSequence::where('year', $year)->lockForUpdate()->first();

            // 3. Add one and save
            $sequence->last_number = $sequence->last_number + 1;
            $sequence->save();

            // 4. Build the text: %05d means "at least 5 digits, padded with zeros"
            return sprintf('RDAC-%d-%05d', $year, $sequence->last_number);
        });
    }
}