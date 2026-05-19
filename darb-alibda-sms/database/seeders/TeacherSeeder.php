<?php

namespace Database\Seeders;

use App\Models\Academic\Teacher;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherRole = Role::firstWhere('name', 'teacher');

        if (! $teacherRole) {
            return;
        }

        $teachers = [
            [
                'email' => 'teacher1@example.com',
                'name' => 'أحمد محمد',
                'phone' => '0501111111',
                'first_name' => 'أحمد',
                'last_name' => 'محمد',
                'national_id' => 'TCH0012345',
                'registry_number' => 'TREG001',
                'specialization' => 'mathematics',
                'employee_number' => 'EMP001',
                'hire_date' => '2020-09-01',
                'employment_type' => 'full_time',
                'grade' => 'A',
                'address' => 'الرياض',
                'phone_alt' => '0501111122',
                'experience_years' => 5,
            ],
            [
                'email' => 'teacher2@example.com',
                'name' => 'سارة علي',
                'phone' => '0502222222',
                'first_name' => 'سارة',
                'last_name' => 'علي',
                'national_id' => 'TCH0023456',
                'registry_number' => 'TREG002',
                'specialization' => 'arabic',
                'employee_number' => 'EMP002',
                'hire_date' => '2021-02-15',
                'employment_type' => 'part_time',
                'grade' => 'B',
                'address' => 'جدة',
                'phone_alt' => '0502222233',
                'experience_years' => 3,
            ],
        ];

        foreach ($teachers as $teacherData) {
            $user = User::updateOrCreate(
                ['email' => $teacherData['email']],
                [
                    'name' => $teacherData['name'],
                    'email' => $teacherData['email'],
                    'phone' => $teacherData['phone'],
                    'role_id' => $teacherRole->id,
                    'password' => Hash::make('password'),
                ]
            );

            Teacher::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $teacherData['first_name'],
                    'last_name' => $teacherData['last_name'],
                    'national_id' => $teacherData['national_id'],
                    'registry_number' => $teacherData['registry_number'],
                    'specialization' => $teacherData['specialization'],
                    'employee_number' => $teacherData['employee_number'],
                    'hire_date' => $teacherData['hire_date'],
                    'employment_type' => $teacherData['employment_type'],
                    'grade' => $teacherData['grade'],
                    'address' => $teacherData['address'],
                    'phone_alt' => $teacherData['phone_alt'],
                    'experience_years' => $teacherData['experience_years'],
                ]
            );
        }
    }
}
