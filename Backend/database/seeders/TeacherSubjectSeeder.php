<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSubjectSeeder extends Seeder
{
    public function run()
    {
        DB::table('teachers_subject')->insert([
            [
                'teacher_id' => 1,
                'subject_id' => 1,
                'subject_code' => 'MATH-101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 1,
                'subject_id' => 2,
                'subject_code' => 'SCI-101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'teacher_id' => 2,
                'subject_id' => 1,
                'subject_code' => 'MATH-101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more as needed
        ]);
    }
}