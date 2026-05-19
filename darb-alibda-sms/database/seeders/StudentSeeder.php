<?php

namespace Database\Seeders;

use App\Models\Academic\Student;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentRole = Role::firstWhere('name', 'student');
        $parentRole = Role::firstWhere('name', 'parent');

        if (! $studentRole || ! $parentRole) {
            return;
        }

        $parent1 = User::firstWhere('email', 'parent1@example.com');
        $parent2 = User::firstWhere('email', 'parent2@example.com');

        $students = [
            [
                'email' => 'student1@example.com',
                'name' => 'باسم أحمد',
                'phone' => '0505555555',
                'first_name' => 'باسم',
                'last_name' => 'أحمد',
                'father_name' => 'محمد',
                'mother_name' => 'سارة',
                'national_id' => '1234567890',
                'registry_number' => 'STU001',
                'birth_date' => '2010-05-15',
                'gender' => 'male',
                'parent_id' => $parent1?->id,
            ],
            [
                'email' => 'student2@example.com',
                'name' => 'لمى خالد',
                'phone' => '0506666666',
                'first_name' => 'لمى',
                'last_name' => 'خالد',
                'father_name' => 'خالد',
                'mother_name' => 'منى',
                'national_id' => '2345678901',
                'registry_number' => 'STU002',
                'birth_date' => '2011-08-20',
                'gender' => 'female',
                'parent_id' => $parent2?->id,
            ],
        ];

        foreach ($students as $studentData) {
            $user = User::updateOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'email' => $studentData['email'],
                    'phone' => $studentData['phone'],
                    'role_id' => $studentRole->id,
                    'password' => Hash::make('password'),
                ]
            );

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'parent_id' => $studentData['parent_id'],
                    'first_name' => $studentData['first_name'],
                    'last_name' => $studentData['last_name'],
                    'father_name' => $studentData['father_name'],
                    'mother_name' => $studentData['mother_name'],
                    'national_id' => $studentData['national_id'],
                    'registry_number' => $studentData['registry_number'],
                    'birth_date' => $studentData['birth_date'],
                    'gender' => $studentData['gender'],
                ]
            );
        }
    }
}
