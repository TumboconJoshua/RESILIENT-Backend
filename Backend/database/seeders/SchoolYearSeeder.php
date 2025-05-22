<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\SchoolYear;

class SchoolYearSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        SchoolYear::truncate();
        Schema::enableForeignKeyConstraints();

        // Seed your data here
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