<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use App\Enums\Gender;
use App\Enums\MarkResult;
use App\Enums\StudentStatus;
use App\Enums\SubjectComponentType;
use App\Enums\TermType;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Student;
use App\Models\Academic\StudentEnrollment;
use App\Models\Academic\Teacher;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Communication\News;
use App\Models\Grading\StudentMark;
use App\Models\Grading\StudentSubjectResult;
use App\Models\Subjects\Subject;
use App\Models\Subjects\SubjectComponent;
use App\Models\Subjects\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WaleedTeacherScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $teacherRole = Role::firstWhere('name', 'teacher');
        $studentRole = Role::firstWhere('name', 'student');
        $parentRole = Role::firstWhere('name', 'parent');
        $adminUser = User::whereHas('role', fn ($query) => $query->where('name', 'admin'))->first();

        if (! $teacherRole || ! $studentRole || ! $parentRole) {
            $this->command->warn('Required roles not found. Skipping WaleedTeacherScenarioSeeder.');

            return;
        }

        $teacherUser = User::updateOrCreate(
            ['email' => 'waleed.alsayed@example.com'],
            [
                'name' => 'وائل السيد خليل',
                'phone' => '0985837696',
                'role_id' => $teacherRole->id,
                'password' => bcrypt('Password123'),
                'is_active' => true,
            ]
        );

        $teacher = Teacher::updateOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'first_name' => 'وائل',
                'last_name' => 'السيد خليل',
                'father_name' => 'محمد',
                'mother_name' => '......',
                'national_id' => 'TCHR-2026-001',
                'registry_number' => 'REG-2026-WALEED',
                'specialization' => 'mathematics',
                'employee_number' => 'EMP-0985837696',
                'hire_date' => '2003-09-01',
                'employment_type' => 'full_time',
                'address' => 'دمشق',
                'phone_alt' => '0990000001',
                'gender' => Gender::MALE->value,
                'birth_date' => '1990-04-12',
                'experience_years' => 6,
            ]
        );

        $class = SchoolClass::updateOrCreate(
            ['type' => ClassType::PRIMARY_SIXTH->value],
            []
        );

        $section = Section::updateOrCreate(
            ['class_id' => $class->id, 'name' => 'الرابعة'],
            ['capacity' => 30]
        );

        $terms = [
            [
                'type' => TermType::FIRST_TERM->value,
                'academic_year' => '2025-2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-15',
            ],
            [
                'type' => TermType::SECOND_TERM->value,
                'academic_year' => '2025-2026',
                'start_date' => '2026-01-16',
                'end_date' => '2026-06-15',
            ],
        ];

        $termMap = [];

        foreach ($terms as $termData) {
            $term = Term::updateOrCreate(
                ['type' => $termData['type'], 'academic_year' => $termData['academic_year']],
                [
                    'start_date' => $termData['start_date'],
                    'end_date' => $termData['end_date'],
                ]
            );

            $termMap[$termData['type']] = $term->id;
        }

        $subjectDefinitions = [
            [
                'name' => 'الرياضيات',
                'code' => 'MATH-6-4',
                'description' => 'مادة الرياضيات للشعبة الرابعة للصف السادس',
                'pass_mark' => 50,
                'full_mark' => 100,
            ],
            [
                'name' => 'العلوم',
                'code' => 'SCI-6-4',
                'description' => 'مادة العلوم للشعبة الرابعة للصف السادس',
                'pass_mark' => 50,
                'full_mark' => 100,
            ],
        ];

        $subjectMap = [];

        foreach ($subjectDefinitions as $subjectData) {
            $subject = Subject::updateOrCreate(
                ['code' => $subjectData['code']],
                [
                    'name' => $subjectData['name'],
                    'description' => $subjectData['description'],
                    'pass_mark' => $subjectData['pass_mark'],
                    'full_mark' => $subjectData['full_mark'],
                    'class_id' => $class->id,
                    'teacher_id' => $teacher->id,
                    'num_of_weekly_hours' => 4,
                ]
            );

            $subjectMap[$subjectData['name']] = $subject;

            $components = [
                ['type' => SubjectComponentType::WRITTEN->value, 'out_of' => 50, 'order' => 1],
                ['type' => SubjectComponentType::ORAL->value, 'out_of' => 30, 'order' => 2],
                ['type' => SubjectComponentType::PRACTICAL->value, 'out_of' => 20, 'order' => 3],
            ];

            foreach ($components as $index => $componentData) {
                SubjectComponent::updateOrCreate(
                    ['subject_id' => $subject->id, 'type' => $componentData['type']],
                    [
                        'out_of' => $componentData['out_of'],
                        'order' => $componentData['order'],
                        'description' => $subjectData['name'] . ' - ' . $componentData['type'],
                    ]
                );
            }
        }

        $parentNames = [
            'حسن علي',
            'سامي محمد',
            'إبراهيم خليل',
            'محمود حسن',
            'أحمد رامي',
            'خالد ياسر',
            'عبد الرحمن نادر',
        ];

        $parentUsers = [];

        foreach ($parentNames as $index => $parentName) {
            $parentUser = User::updateOrCreate(
                ['email' => 'parent_' . ($index + 1) . '@example.com'],
                [
                    'name' => $parentName,
                    'phone' => '09' . sprintf('%08d', 10000000 + $index),
                    'role_id' => $parentRole->id,
                    'password' => bcrypt('Password123'),
                    'is_active' => true,
                ]
            );

            $parentUsers[] = $parentUser;
        }

        $studentData = [
            ['name' => 'عمر أحمد', 'first_name' => 'عمر', 'father_name' => 'أحمد', 'last_name' => 'نزار', 'gender' => Gender::MALE, 'birth_date' => '2014-02-04', 'email' => 'student_01@example.com', 'parent_index' => 0],
            ['name' => 'ريم حسين', 'first_name' => 'ريم', 'father_name' => 'حسين', 'last_name' => 'عبدالله', 'gender' => Gender::FEMALE, 'birth_date' => '2014-03-10', 'email' => 'student_02@example.com', 'parent_index' => 0],
            ['name' => 'سارة أحمد', 'first_name' => 'سارة', 'father_name' => 'أحمد', 'last_name' => 'غانم', 'gender' => Gender::FEMALE, 'birth_date' => '2014-05-12', 'email' => 'student_03@example.com', 'parent_index' => 0],
            ['name' => 'حسن يوسف', 'first_name' => 'حسن', 'father_name' => 'يوسف', 'last_name' => 'مقداد', 'gender' => Gender::MALE, 'birth_date' => '2014-06-07', 'email' => 'student_04@example.com', 'parent_index' => 1],
            ['name' => 'مها خليل', 'first_name' => 'مها', 'father_name' => 'خليل', 'last_name' => 'درويش', 'gender' => Gender::FEMALE, 'birth_date' => '2014-08-12', 'email' => 'student_05@example.com', 'parent_index' => 1],
            ['name' => 'أحمد سامي', 'first_name' => 'أحمد', 'father_name' => 'سامي', 'last_name' => 'باسل', 'gender' => Gender::MALE, 'birth_date' => '2014-09-15', 'email' => 'student_06@example.com', 'parent_index' => 1],
            ['name' => 'ليان عادل', 'first_name' => 'ليان', 'father_name' => 'عادل', 'last_name' => 'محمود', 'gender' => Gender::FEMALE, 'birth_date' => '2014-11-21', 'email' => 'student_07@example.com', 'parent_index' => 2],
            ['name' => 'جود عبد الله', 'first_name' => 'جود', 'father_name' => 'عبد الله', 'last_name' => 'قاسم', 'gender' => Gender::FEMALE, 'birth_date' => '2014-12-09', 'email' => 'student_08@example.com', 'parent_index' => 2],
            ['name' => 'مروان رامي', 'first_name' => 'مروان', 'father_name' => 'رامي', 'last_name' => 'خالد', 'gender' => Gender::MALE, 'birth_date' => '2015-01-17', 'email' => 'student_09@example.com', 'parent_index' => 2],
            ['name' => 'يارا ناصر', 'first_name' => 'يارا', 'father_name' => 'ناصر', 'last_name' => 'شربتجي', 'gender' => Gender::FEMALE, 'birth_date' => '2015-02-20', 'email' => 'student_10@example.com', 'parent_index' => 3],
            ['name' => 'تيم حسن', 'first_name' => 'تيم', 'father_name' => 'حسن', 'last_name' => 'الرز', 'gender' => Gender::MALE, 'birth_date' => '2015-03-06', 'email' => 'student_11@example.com', 'parent_index' => 3],
            ['name' => 'سارة محمود', 'first_name' => 'سارة', 'father_name' => 'محمود', 'last_name' => 'هاني', 'gender' => Gender::FEMALE, 'birth_date' => '2015-04-18', 'email' => 'student_12@example.com', 'parent_index' => 3],
            ['name' => 'محمود جورج', 'first_name' => 'محمود', 'father_name' => 'جورج', 'last_name' => 'خضر', 'gender' => Gender::MALE, 'birth_date' => '2015-05-29', 'email' => 'student_13@example.com', 'parent_index' => 4],
            ['name' => 'نورا ياسين', 'first_name' => 'نورا', 'father_name' => 'ياسين', 'last_name' => 'أمين', 'gender' => Gender::FEMALE, 'birth_date' => '2015-07-05', 'email' => 'student_14@example.com', 'parent_index' => 4],
            ['name' => 'فهد لؤي', 'first_name' => 'فهد', 'father_name' => 'لؤي', 'last_name' => 'عيسى', 'gender' => Gender::MALE, 'birth_date' => '2015-08-08', 'email' => 'student_15@example.com', 'parent_index' => 4],
            ['name' => 'لينا أسعد', 'first_name' => 'لينا', 'father_name' => 'أسعد', 'last_name' => 'شهاب', 'gender' => Gender::FEMALE, 'birth_date' => '2015-09-24', 'email' => 'student_16@example.com', 'parent_index' => 5],
            ['name' => 'زياد جورج', 'first_name' => 'زياد', 'father_name' => 'جورج', 'last_name' => 'حسن', 'gender' => Gender::MALE, 'birth_date' => '2015-10-14', 'email' => 'student_17@example.com', 'parent_index' => 5],
            ['name' => 'رغد رامي', 'first_name' => 'رغد', 'father_name' => 'رامي', 'last_name' => 'قاسم', 'gender' => Gender::FEMALE, 'birth_date' => '2015-11-12', 'email' => 'student_18@example.com', 'parent_index' => 5],
            ['name' => 'إياد ماهر', 'first_name' => 'إياد', 'father_name' => 'ماهر', 'last_name' => 'بدر', 'gender' => Gender::MALE, 'birth_date' => '2015-12-16', 'email' => 'student_19@example.com', 'parent_index' => 6],
            ['name' => 'جنى قيس', 'first_name' => 'جنى', 'father_name' => 'قيس', 'last_name' => 'سالم', 'gender' => Gender::FEMALE, 'birth_date' => '2016-01-23', 'email' => 'student_20@example.com', 'parent_index' => 6],
        ];

        $studentsWithMarks = [0, 1, 2, 4, 6, 8, 10, 12, 15, 17];

        foreach ($studentData as $index => $studentInfo) {
            $user = User::updateOrCreate(
                ['email' => $studentInfo['email']],
                [
                    'name' => $studentInfo['name'],
                    'phone' => '09' . sprintf('%08d', 20000000 + $index),
                    'role_id' => $studentRole->id,
                    'password' => bcrypt('Password123'),
                    'is_active' => true,
                ]
            );

            $student = Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'parent_id' => $parentUsers[$studentInfo['parent_index']]->id,
                    'first_name' => $studentInfo['first_name'],
                    'last_name' => $studentInfo['last_name'],
                    'father_name' => $studentInfo['father_name'],
                    'mother_name' => 'أم ' . $studentInfo['first_name'],
                    'national_id' => 'STD-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'registry_number' => 'REG-ST-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'birth_date' => $studentInfo['birth_date'],
                    'gender' => $studentInfo['gender']->value,
                ]
            );

            $enrollment = StudentEnrollment::updateOrCreate(
                ['student_id' => $student->id, 'academic_year' => '2025-2026'],
                [
                    'section_id' => $section->id,
                    'enrollment_date' => '2025-09-01',
                    'status' => StudentStatus::ACTIVE->value,
                    'final_result' => MarkResult::PENDING->value,
                    'final_average' => null,
                    'notes' => 'طالب تابع للمعلم وائل السيد خليل في الصف السادس الشعبة الرابعة',
                ]
            );

            foreach ($subjectMap as $subjectName => $subject) {
                $subjectResult = StudentSubjectResult::updateOrCreate(
                    ['enrollment_id' => $enrollment->id, 'subject_id' => $subject->id],
                    [
                        'term1_mark' => null,
                        'term2_mark' => null,
                        'yearly_mark' => null,
                        'result' => MarkResult::PENDING->value,
                    ]
                );

                if (! in_array($index, $studentsWithMarks, true)) {
                    continue;
                }

                $components = $subject->components()->orderBy('order')->get();

                $term1Total = 0;
                $term2Total = 0;

                foreach ($components as $componentIndex => $component) {
                    $base = $index * 3 + $componentIndex + 1;
                    $term1Mark = min(100, (($base * 5) % 45) + 30);
                    $term2Mark = min(100, (($base * 7) % 40) + 25);

                    $term1Total += $term1Mark;
                    $term2Total += $term2Mark;

                    StudentMark::updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'subject_id' => $subject->id,
                            'subject_component_id' => $component->id,
                            'term_id' => $termMap[TermType::FIRST_TERM->value],
                        ],
                        ['mark' => round($term1Mark, 2)]
                    );

                    StudentMark::updateOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'subject_id' => $subject->id,
                            'subject_component_id' => $component->id,
                            'term_id' => $termMap[TermType::SECOND_TERM->value],
                        ],
                        ['mark' => round($term2Mark, 2)]
                    );
                }

                $yearlyAverage = round((($term1Total / 3) + ($term2Total / 3)) / 2, 2);
                $result = $yearlyAverage >= $subject->pass_mark ? MarkResult::PASS : MarkResult::FAIL;

                $subjectResult->update([
                    'term1_mark' => round($term1Total / 3, 2),
                    'term2_mark' => round($term2Total / 3, 2),
                    'yearly_mark' => $yearlyAverage,
                    'result' => $result->value,
                ]);
            }
        }

        $notifications = [
            [
                'title' => 'تنبيه إداري',
                'body' => 'يرجى مراجعة جدول الحصص الأسبوعي وتحديث درجات الطلاب خلال اليوم.',
                'from' => 'admin',
            ],
            [
                'title' => 'رسالة من ولي أمر',
                'body' => 'أرغب في معرفة مستوى ابني في مادة الرياضيات قبل موعد الاختبار القادم.',
                'from' => 'parent',
            ],
            [
                'title' => 'إشعار طالب',
                'body' => 'طلب الطالب من المعلم توضيح نصائح الواجب المنزلي في العلوم.',
                'from' => 'student',
            ],
            [
                'title' => 'تذكير إداري',
                'body' => 'من المهم إبلاغ أولياء الأمور بنتائج التحصيل للطلاب في الشعبة الرابعة.',
                'from' => 'admin',
            ],
            [
                'title' => 'مراسلة ولي أمر',
                'body' => 'يرجى إرسال ملخص تقدم الطالب في مادة العلوم في نهاية الأسبوع.',
                'from' => 'parent',
            ],
        ];

        foreach ($notifications as $notificationData) {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SystemNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $teacherUser->id,
                'data' => json_encode([
                    'title' => $notificationData['title'],
                    'body' => $notificationData['body'],
                    'from' => $notificationData['from'],
                    'teacher_name' => $teacherUser->name,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $newsItems = [
            [
                'title' => 'تحديث مهم في مادة الرياضيات',
                'body' => 'سيتم عقد درس إضافي لتحضير الاختبار النهائي لمادة الرياضيات خلال هذا الأسبوع.',
                'audience' => 'teachers',
            ],
            [
                'title' => 'مراجعة شاملة في العلوم',
                'body' => 'يرجى متابعة البوابة لتجهيز الطلاب لواجبات العلوم والأنشطة العملية.',
                'audience' => 'all',
            ],
            [
                'title' => 'تنبيه لطلاب الشعبة الرابعة',
                'body' => 'يُطلب من الطلاب الالتزام بموعد تسليم الواجبات وتفعيل حضور الحصص.',
                'audience' => 'students',
            ],
        ];

        foreach ($newsItems as $newsItem) {
            News::create([
                'title' => $newsItem['title'],
                'body' => $newsItem['body'],
                'audience' => $newsItem['audience'],
                'created_by' => $adminUser?->id ?? $teacherUser->id,
            ]);
        }

        $this->command->info('Created teacher scenario for Wael with 20 students, subjects, marks and notifications.');
    }
}
