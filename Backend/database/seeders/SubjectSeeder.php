<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        Subject::create([
            'SubjectName' => 'Mathematics',
            'SubjectCode' => 101,
            'Track' => 'Academic',
            'Teacher_ID' => 1
        ]);
    }
}