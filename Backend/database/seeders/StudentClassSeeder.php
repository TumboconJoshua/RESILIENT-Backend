<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear the table first (optional)
        DB::table('student_class')->truncate();

        // Sample data
        $studentClasses = [
            [
                'Student_ID' => 1, // Replace with actual student ID
                'Class_ID' => 2,   // Replace with actual class ID
                'SY_ID' => 1,      // Replace with actual school year ID
                'Subject_ID' => 1, // Replace with actual subject ID
                'Teacher_ID' => 1, // Replace with actual teacher ID
                'Section' => 'A',
                'Status' => 'Approved',
                'isAdvisory' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data
        DB::table('student_class')->insert($studentClasses);
    }
}
