<?php

namespace Database\Seeders;

use App\Models\ClassesModel;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    public function run()
    {
        ClassesModel::create([
            'ClassName' => 'Grade 7-A',
            'Section' => 'A',
            'SY_ID' => 1,  // Must exist in school_years table
            'Grade_Level' => '7',
            'Track' => 'Academic',
            'classType' => 'Advisory',
            'Teacher_ID' => 1  // Must exist in teachers table
        ]);
    }
}