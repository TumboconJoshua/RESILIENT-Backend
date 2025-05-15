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

        // Create admin teacher
        DB::table('teachers')->insert([
            'EmployeeNo' => 'TCH001',
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create 100 regular teachers
    }
}