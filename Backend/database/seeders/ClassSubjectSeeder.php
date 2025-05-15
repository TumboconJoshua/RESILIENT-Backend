<?php

namespace Database\Seeders;

use App\Models\ClassSubject;
use Illuminate\Database\Seeder;

class ClassSubjectSeeder extends Seeder
{
    public function run()
    {
        $enrollments = [
            [
                'Student_ID' => 1,   // Must exist in students table
                'Class_ID' => 1,     // Must exist in classes table
                'SY_ID' => 1,        // Must exist in school_years table
                'Teacher_ID' => 1,   // Must exist in teachers table
                'Subject_ID' => 1    // Must exist in subjects table
            ],
        ];

        foreach ($enrollments as $enrollment) {
            ClassSubject::create($enrollment);
        }
    }
}