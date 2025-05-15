<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolYearSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data to avoid duplicates
        DB::table('school_years')->truncate();

        // Seed the data
        SchoolYear::create([
            'SY_ID' => 1,
            'Start_Date' => '2023-06-01',
            'End_Date' => '2024-03-31',
            'SY_Year' => '2023-2024',
            'created_at' => '2025-05-15 18:43:29',
            'updated_at' => '2025-05-15 18:43:29',
        ]);
    }
}