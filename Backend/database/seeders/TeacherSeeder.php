<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create SuperAdmin teacher
        DB::table('teachers')->insert([
            'EmployeeNo' => 'SUPERADMIN001',
            'Email' => 'superadmin@example.com',
            'Password' => Hash::make('superadmin123'),
            'FirstName' => 'Super',
            'LastName' => 'Admin',
            'MiddleName' => null,
            'BirthDate' => '1975-01-01',
            'Sex' => 'M',
            'Position' => 'SuperAdmin',
            'ContactNumber' => '09170000001',
            'Address' => 'SuperAdmin Address',
            'Educational_Attainment' => 'Master\'s Degree',
            'Teaching_Position' => 'Teacher 1',

            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Admin teacher
        DB::table('teachers')->insert([
            'EmployeeNo' => 'ADMIN001',
            'Email' => 'admin@example.com',
            'Password' => Hash::make('admin123'),
            'FirstName' => 'System',
            'LastName' => 'Administrator',
            'MiddleName' => null,
            'BirthDate' => '1980-01-01',
            'Sex' => 'M',
            'Position' => 'Admin',
            'ContactNumber' => '09171234567',
            'Address' => 'Admin Address',
            'Educational_Attainment' => 'Master\'s Degree',
            'Teaching_Position' => 'Teacher 1',

            
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create regular Teacher account
        DB::table('teachers')->insert([
            'EmployeeNo' => 'TEACHER001',
            'Email' => 'teacher@example.com',
            'Password' => Hash::make('teacher123'),
            'FirstName' => 'Juan',
            'LastName' => 'Dela Cruz',
            'MiddleName' => 'Santos',
            'BirthDate' => '1990-05-10',
            'Sex' => 'F',
            'Position' => 'Teacher',
            'ContactNumber' => '09172345678',
            'Address' => 'Teacher Address',
            'Educational_Attainment' => 'Master\'s Degree',
            'Teaching_Position' => 'Teacher 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
    