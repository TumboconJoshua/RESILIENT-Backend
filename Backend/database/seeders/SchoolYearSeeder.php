<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    public function run()
    {
        SchoolYear::create([
            'Start_Date' => '2023-06-01',
            'End_Date' => '2024-03-31', 
            'SY_Year' => '2023-2024'
        ]);
    }
}