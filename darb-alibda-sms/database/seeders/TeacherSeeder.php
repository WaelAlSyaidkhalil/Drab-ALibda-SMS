<?php

namespace Database\Seeders;

use App\Models\Academic\Teacher;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Services\Admin\GeneratePasswordService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(GeneratePasswordService $generatePasswordService): void
    {
        $teacherRole = Role::firstWhere('name', 'teacher');

        if (! $teacherRole) {
            return;
        }

        $teachers = [
            [
                'email' => 'teacher1@example.com',
                'name' => 'أحمد محمد',
                'phone' => '0501000001',
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
                'phone_alt' => '0501000101',
            ],
            [
                'email' => 'teacher2@example.com',
                'name' => 'سارة علي',
                'phone' => '0501000002',
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
                'phone_alt' => '0501000102',
            ],
            [
                'email' => 'teacher3@example.com',
                'name' => 'خالد حسن',
                'phone' => '0501000003',
                'first_name' => 'خالد',
                'last_name' => 'حسن',
                'national_id' => 'TCH0034567',
                'registry_number' => 'TREG003',
                'specialization' => 'science',
                'employee_number' => 'EMP003',
                'hire_date' => '2019-05-10',
                'employment_type' => 'full_time',
                'grade' => 'A',
                'address' => 'الدمام',
                'phone_alt' => '0501000103',
            ],
            [
                'email' => 'teacher4@example.com',
                'name' => 'مريم محمود',
                'phone' => '0501000004',
                'first_name' => 'مريم',
                'last_name' => 'محمود',
                'national_id' => 'TCH0045678',
                'registry_number' => 'TREG004',
                'specialization' => 'english',
                'employee_number' => 'EMP004',
                'hire_date' => '2022-01-10',
                'employment_type' => 'full_time',
                'grade' => 'B',
                'address' => 'مكة',
                'phone_alt' => '0501000104',
            ],
            [
                'email' => 'teacher5@example.com',
                'name' => 'عمر فاروق',
                'phone' => '0501000005',
                'first_name' => 'عمر',
                'last_name' => 'فاروق',
                'national_id' => 'TCH0056789',
                'registry_number' => 'TREG005',
                'specialization' => 'physics',
                'employee_number' => 'EMP005',
                'hire_date' => '2018-09-01',
                'employment_type' => 'full_time',
                'grade' => 'A',
                'address' => 'المدينة المنورة',
                'phone_alt' => '0501000105',
            ],
            [
                'email' => 'teacher6@example.com',
                'name' => 'فاطمة الزهراء',
                'phone' => '0501000006',
                'first_name' => 'فاطمة',
                'last_name' => 'الزهراء',
                'national_id' => 'TCH0067890',
                'registry_number' => 'TREG006',
                'specialization' => 'chemistry',
                'employee_number' => 'EMP006',
                'hire_date' => '2021-08-20',
                'employment_type' => 'full_time',
                'grade' => 'B',
                'address' => 'الخبر',
                'phone_alt' => '0501000106',
            ],
            [
                'email' => 'teacher7@example.com',
                'name' => 'يوسف إبراهيم',
                'phone' => '0501000007',
                'first_name' => 'يوسف',
                'last_name' => 'إبراهيم',
                'national_id' => 'TCH0078901',
                'registry_number' => 'TREG007',
                'specialization' => 'biology',
                'employee_number' => 'EMP007',
                'hire_date' => '2020-03-15',
                'employment_type' => 'part_time',
                'grade' => 'B',
                'address' => 'أبها',
                'phone_alt' => '0501000107',
            ],
            [
                'email' => 'teacher8@example.com',
                'name' => 'خديجة عبد الله',
                'phone' => '0501000008',
                'first_name' => 'خديجة',
                'last_name' => 'عبد الله',
                'national_id' => 'TCH0089012',
                'registry_number' => 'TREG008',
                'specialization' => 'history',
                'employee_number' => 'EMP008',
                'hire_date' => '2017-11-01',
                'employment_type' => 'full_time',
                'grade' => 'A',
                'address' => 'تبوك',
                'phone_alt' => '0501000108',
            ],
            [
                'email' => 'teacher9@example.com',
                'name' => 'طارق العلي',
                'phone' => '0501000009',
                'first_name' => 'طارق',
                'last_name' => 'العلي',
                'national_id' => 'TCH0090123',
                'registry_number' => 'TREG009',
                'specialization' => 'geography',
                'employee_number' => 'EMP009',
                'hire_date' => '2022-09-01',
                'employment_type' => 'full_time',
                'grade' => 'C',
                'address' => 'حائل',
                'phone_alt' => '0501000109',
            ],
            [
                'email' => 'teacher10@example.com',
                'name' => 'رانيا التميمي',
                'phone' => '0501000010',
                'first_name' => 'رانيا',
                'last_name' => 'التميمي',
                'national_id' => 'TCH0101234',
                'registry_number' => 'TREG010',
                'specialization' => 'computer',
                'employee_number' => 'EMP010',
                'hire_date' => '2023-02-01',
                'employment_type' => 'full_time',
                'grade' => 'C',
                'address' => 'القصيم',
                'phone_alt' => '0501010102',
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
                    'password' => $generatePasswordService->generatePassword(),
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
                ]
            );
        }
    }
}
