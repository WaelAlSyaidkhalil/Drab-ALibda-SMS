<?php

namespace Database\Seeders;

use App\Models\Academic\Student;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Services\Admin\GeneratePasswordService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(GeneratePasswordService $generatePasswordService): void
    {
        $studentRole = Role::firstWhere('name', 'student');
        $parentRole = Role::firstWhere('name', 'parent');

        if (! $studentRole || ! $parentRole) {
            return;
        }

        $parentMap = [];
        for ($p = 1; $p <= 10; $p++) {
            $pUser = User::firstWhere('email', "parent{$p}@example.com");
            if ($pUser) {
                $parentMap[$p] = $pUser->id;
            }
        }

        $students = [
            [
                'email' => 'student1@example.com',
                'name' => 'باسم أحمد',
                'phone' => '0505555001',
                'first_name' => 'باسم',
                'last_name' => 'أحمد',
                'father_name' => 'محمد',
                'mother_name' => 'سارة',
                'national_id' => '1234567890',
                'registry_number' => 'STU001',
                'birth_date' => '2010-05-15',
                'gender' => 'male',
                'parent_id' => $parentMap[1] ?? null,
            ],
            [
                'email' => 'student2@example.com',
                'name' => 'لمى خالد',
                'phone' => '0505555002',
                'first_name' => 'لمى',
                'last_name' => 'خالد',
                'father_name' => 'خالد',
                'mother_name' => 'منى',
                'national_id' => '2345678901',
                'registry_number' => 'STU002',
                'birth_date' => '2011-08-20',
                'gender' => 'female',
                'parent_id' => $parentMap[2] ?? null,
            ],
            [
                'email' => 'student3@example.com',
                'name' => 'طارق محمد',
                'phone' => '0503000003',
                'first_name' => 'طارق',
                'last_name' => 'محمد',
                'father_name' => 'محمد',
                'mother_name' => 'فاطمة',
                'national_id' => '3456789012',
                'registry_number' => 'STU003',
                'birth_date' => '2012-01-10',
                'gender' => 'male',
                'parent_id' => $parentMap[3] ?? null,
            ],
            [
                'email' => 'student4@example.com',
                'name' => 'هند الشمري',
                'phone' => '0503000004',
                'first_name' => 'هند',
                'last_name' => 'الشمري',
                'father_name' => 'خالد',
                'mother_name' => 'عائشة',
                'national_id' => '4567890123',
                'registry_number' => 'STU004',
                'birth_date' => '2011-03-25',
                'gender' => 'female',
                'parent_id' => $parentMap[4] ?? null,
            ],
            [
                'email' => 'student5@example.com',
                'name' => 'يوسف الحربي',
                'phone' => '0503000005',
                'first_name' => 'يوسف',
                'last_name' => 'الحربي',
                'father_name' => 'عبدالله',
                'mother_name' => 'زينة',
                'national_id' => '5678901234',
                'registry_number' => 'STU005',
                'birth_date' => '2010-11-12',
                'gender' => 'male',
                'parent_id' => $parentMap[5] ?? null,
            ],
            [
                'email' => 'student6@example.com',
                'name' => 'سلمى القحطاني',
                'phone' => '0503000006',
                'first_name' => 'سلمى',
                'last_name' => 'القحطاني',
                'father_name' => 'سليمان',
                'mother_name' => 'أمينة',
                'national_id' => '6789012345',
                'registry_number' => 'STU006',
                'birth_date' => '2012-07-04',
                'gender' => 'female',
                'parent_id' => $parentMap[6] ?? null,
            ],
            [
                'email' => 'student7@example.com',
                'name' => 'فيصل الغامدي',
                'phone' => '0503000007',
                'first_name' => 'فيصل',
                'last_name' => 'الغامدي',
                'father_name' => 'إبراهيم',
                'mother_name' => 'مريم',
                'national_id' => '7890123456',
                'registry_number' => 'STU007',
                'birth_date' => '2011-09-18',
                'gender' => 'male',
                'parent_id' => $parentMap[7] ?? null,
            ],
            [
                'email' => 'student8@example.com',
                'name' => 'نورة العتيبي',
                'phone' => '0503000008',
                'first_name' => 'نورة',
                'last_name' => 'العتيبي',
                'father_name' => 'ياسر',
                'mother_name' => 'هدى',
                'national_id' => '8901234567',
                'registry_number' => 'STU008',
                'birth_date' => '2013-02-14',
                'gender' => 'female',
                'parent_id' => $parentMap[8] ?? null,
            ],
            [
                'email' => 'student9@example.com',
                'name' => 'زياد الدوسري',
                'phone' => '0503000009',
                'first_name' => 'زياد',
                'last_name' => 'الدوسري',
                'father_name' => 'فهد',
                'mother_name' => 'عبير',
                'national_id' => '9012345678',
                'registry_number' => 'STU009',
                'birth_date' => '2010-04-30',
                'gender' => 'male',
                'parent_id' => $parentMap[9] ?? null,
            ],
            [
                'email' => 'student10@example.com',
                'name' => 'ريم السعيد',
                'phone' => '0503000010',
                'first_name' => 'ريم',
                'last_name' => 'السعيد',
                'father_name' => 'صالح',
                'mother_name' => 'منى',
                'national_id' => '0123456789',
                'registry_number' => 'STU010',
                'birth_date' => '2012-06-22',
                'gender' => 'female',
                'parent_id' => $parentMap[10] ?? null,
            ],
            [
                'email' => 'student11@example.com',
                'name' => 'عبد العزيز النجار',
                'phone' => '0503000011',
                'first_name' => 'عبد العزيز',
                'last_name' => 'النجار',
                'father_name' => 'محمد',
                'mother_name' => 'سارة',
                'national_id' => '1122334455',
                'registry_number' => 'STU011',
                'birth_date' => '2011-10-05',
                'gender' => 'male',
                'parent_id' => $parentMap[1] ?? null,
            ],
            [
                'email' => 'student12@example.com',
                'name' => 'شهد الحربي',
                'phone' => '0503000012',
                'first_name' => 'شهد',
                'last_name' => 'الحربي',
                'father_name' => 'خالد',
                'mother_name' => 'منى',
                'national_id' => '2233445566',
                'registry_number' => 'STU012',
                'birth_date' => '2010-12-01',
                'gender' => 'female',
                'parent_id' => $parentMap[2] ?? null,
            ],
            [
                'email' => 'student13@example.com',
                'name' => 'كريم الشمري',
                'phone' => '0503000013',
                'first_name' => 'كريم',
                'last_name' => 'الشمري',
                'father_name' => 'محمد',
                'mother_name' => 'فاطمة',
                'national_id' => '3344556677',
                'registry_number' => 'STU013',
                'birth_date' => '2013-01-19',
                'gender' => 'male',
                'parent_id' => $parentMap[3] ?? null,
            ],
            [
                'email' => 'student14@example.com',
                'name' => 'دانة القحطاني',
                'phone' => '0503000014',
                'first_name' => 'دانة',
                'last_name' => 'القحطاني',
                'father_name' => 'خالد',
                'mother_name' => 'عائشة',
                'national_id' => '4455667788',
                'registry_number' => 'STU014',
                'birth_date' => '2011-05-14',
                'gender' => 'female',
                'parent_id' => $parentMap[4] ?? null,
            ],
            [
                'email' => 'student15@example.com',
                'name' => 'ماجد الغامدي',
                'phone' => '0503000015',
                'first_name' => 'ماجد',
                'last_name' => 'الغامدي',
                'father_name' => 'عبدالله',
                'mother_name' => 'زينة',
                'national_id' => '5566778899',
                'registry_number' => 'STU015',
                'birth_date' => '2012-08-08',
                'gender' => 'male',
                'parent_id' => $parentMap[5] ?? null,
            ],
            [
                'email' => 'student16@example.com',
                'name' => 'فرح العتيبي',
                'phone' => '0503000016',
                'first_name' => 'فرح',
                'last_name' => 'العتيبي',
                'father_name' => 'سليمان',
                'mother_name' => 'أمينة',
                'national_id' => '6677889900',
                'registry_number' => 'STU016',
                'birth_date' => '2010-09-30',
                'gender' => 'female',
                'parent_id' => $parentMap[6] ?? null,
            ],
            [
                'email' => 'student17@example.com',
                'name' => 'حمزة الدوسري',
                'phone' => '0503000017',
                'first_name' => 'حمزة',
                'last_name' => 'الدوسري',
                'father_name' => 'إبراهيم',
                'mother_name' => 'مريم',
                'national_id' => '7788990011',
                'registry_number' => 'STU017',
                'birth_date' => '2011-11-23',
                'gender' => 'male',
                'parent_id' => $parentMap[7] ?? null,
            ],
            [
                'email' => 'student18@example.com',
                'name' => 'حلا السعيد',
                'phone' => '0503000018',
                'first_name' => 'حلا',
                'last_name' => 'السعيد',
                'father_name' => 'ياسر',
                'mother_name' => 'هدى',
                'national_id' => '8899001122',
                'registry_number' => 'STU018',
                'birth_date' => '2012-12-12',
                'gender' => 'female',
                'parent_id' => $parentMap[8] ?? null,
            ],
            [
                'email' => 'student19@example.com',
                'name' => 'وليد النجار',
                'phone' => '0503000019',
                'first_name' => 'وليد',
                'last_name' => 'النجار',
                'father_name' => 'فهد',
                'mother_name' => 'عبير',
                'national_id' => '9900112233',
                'registry_number' => 'STU019',
                'birth_date' => '2010-02-28',
                'gender' => 'male',
                'parent_id' => $parentMap[9] ?? null,
            ],
            [
                'email' => 'student20@example.com',
                'name' => 'مروة الشمري',
                'phone' => '0503000020',
                'first_name' => 'مروة',
                'last_name' => 'الشمري',
                'father_name' => 'صالح',
                'mother_name' => 'منى',
                'national_id' => '0011223344',
                'registry_number' => 'STU020',
                'birth_date' => '2011-07-07',
                'gender' => 'female',
                'parent_id' => $parentMap[10] ?? null,
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
                    'password' => $generatePasswordService->generatePassword(),
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
