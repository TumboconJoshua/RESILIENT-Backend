<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('student_class')->insert([
            //-----------------------------------Grade 7 A----------------------------------- TEACHER 3
            [
                'Student_ID' => 1, // Alice Smith
                'Class_ID' => 1,   // Grade 7 A
                'ClassName' => 'A',
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 3, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Student_ID' => 2, // Bob Johnson
                'Class_ID' => 2,   // A
                'ClassName' => 'B',
                'SY_ID' => 1,      // School Year ID
                'Adviser_ID' => 4, // TEACHER001
                'isAdvisory' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'Student_ID' => 3, // Eve Lopez
            //     'Class_ID' => 1,   // Grade 7 A
            //     'ClassName' => 'Grade 7 C',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 3, // TEACHER001
            //     'isAdvisory' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],

            //-----------------------------------Grade 8 A----------------------------------- TEACHER 4
            // [
            //     'Student_ID' => 4, // Carol Baker
            //     'Class_ID' => 2,   // Grade 7 B
            //     'ClassName' => 'Grade 7 D',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 4, // TEACHER002
            //     'isAdvisory' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'Student_ID' => 5, // Grace Nguyen
            //     'Class_ID' => 2,   // Grade 7 B
            //     'ClassName' => 'Grade 7 B',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 4, // TEACHER002
            //     'isAdvisory' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'Student_ID' => 6, // Henry Reyes
            //     'Class_ID' => 2,   // Grade 7 B
            //     'ClassName' => 'Grade 7 B',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 4, // TEACHER002
            //     'isAdvisory' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],

            // //-----------------------------------Grade 9 A----------------------------------- TEACHER 5
            // [
            //     'Student_ID' => 7, // David Cruz
            //     'Class_ID' => 3,   // Grade 9 A
            //     'ClassName' => 'Grade 8 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 3, // TEACHER003
            //     'isAdvisory' => false,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'Student_ID' => 8, // Frank Garcia
            //     'Class_ID' => 3,   // Grade 8 A
            //     'ClassName' => 'Grade 8 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 3, // TEACHER003
            //     'isAdvisory' => false,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'Student_ID' => 9, // Karen Villanueva
            //     'Class_ID' => 3,   // Grade 8 A
            //     'ClassName' => 'Grade 9 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 3, // TEACHER003
            //     'isAdvisory' => false,  
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],

            // //-----------------------------------Grade 10 A----------------------------------- TEACHER 3
            // [
            //     'Student_ID' => 10, // Isla Torres
            //     'Class_ID' => 4,   // Grade 11 A
            //     'ClassName' => 'Grade 11 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 5, // TEACHER001
            //     'isAdvisory' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'Student_ID' => 11, // Jake Santos
            //     'Class_ID' => 4,   // Grade 11 A
            //     'ClassName' => 'Grade 11 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 5, // TEACHER001
            //     'isAdvisory' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'Student_ID' => 12, // Liam Del Rosario
            //     'Class_ID' => 4,   // Grade 11 A
            //     'ClassName' => 'Grade 11 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 5, // TEACHER001
            //     'isAdvisory' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],

            // //-----------------------------------Grade 12 A----------------------------------- TEACHER 4
            // [
            //     'Student_ID' => 13, // Mia Fernandez
            //     'Class_ID' => 5,   // Grade 12 A
            //     'ClassName' => 'Grade 12 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 5, // TEACHER002
            //     'isAdvisory' => false,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'Student_ID' => 14, // Noah Lim
            //     'Class_ID' => 5,   // Grade 12 A
            //     'ClassName' => 'Grade 12 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 5, // TEACHER002
            //     'isAdvisory' => false,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'Student_ID' => 15, // Olivia Ramos
            //     'Class_ID' => 5,   // Grade 12 A
            //     'ClassName' => 'Grade 12 A',
            //     'SY_ID' => 1,      // School Year ID
            //     'Adviser_ID' => 5, // TEACHER002
            //     'isAdvisory' => false,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ]);
    }
}