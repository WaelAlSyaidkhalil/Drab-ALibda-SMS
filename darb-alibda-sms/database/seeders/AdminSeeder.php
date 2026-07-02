<?php

namespace Database\Seeders;

use App\Enums\AudienceType;
use App\Enums\AttendanceStatus;
use App\Enums\ClassType;
use App\Enums\ComplaintStatus;
use App\Enums\DayOfWeek;
use App\Enums\MarkResult;
use App\Enums\StudentStatus;
use App\Enums\SuggestionStatus;
use App\Enums\TermType;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Student;
use App\Models\Academic\StudentEnrollment;
use App\Models\Academic\Teacher;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Communication\Complaint;
use App\Models\Communication\News;
use App\Models\Communication\Suggestion;
use App\Models\Grading\StudentSubjectResult;
use App\Models\Schedule\Attendance;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TeacherAttendance;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Subject;
use App\Models\Subjects\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $roles = $this->createRoles();

        $admin = $this->createAdminUser($roles['admin']);
        $classes = $this->createSchoolStructure();
        $teachers = $this->createTeachers($roles['teacher']);
        $subjects = $this->createSubjects();
        $parents = $this->createParents($roles['parent']);
        $students = $this->createStudents($roles['student'], $parents);
        $student_enrollments = $this->createEnrollments($students, $classes);
        $this->createStudentAttendances($students);
        $this->createTeacherAttendances($teachers);
        $this->createCommunicationSamples($admin);
        $this->createNewsSamples($admin);
        $this->createSubjectResults($student_enrollments, $subjects);
        $this->createTimeSlots();
        $this->createTerms();
        $this->createSchedules($teachers, $subjects, $classes);
    }

    private function createRoles(): array
    {
        return [
            'admin' => Role::firstOrCreate(['name' => 'admin']),
            'teacher' => Role::firstOrCreate(['name' => 'teacher']),
            'student' => Role::firstOrCreate(['name' => 'student']),
            'parent' => Role::firstOrCreate(['name' => 'parent']),
        ];
    }

    private function createAdminUser(Role $adminRole): User
    {
        return User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'phone' => '0500000000',
                'role_id' => $adminRole->id,
                'is_active' => true,
                'password' => Hash::make('password'),
            ]
        );
    }

    private function createSchoolStructure(): \Illuminate\Database\Eloquent\Collection
    {
        $classTypes = [
            ClassType::PRIMARY_FIRST,
            ClassType::PRIMARY_FOURTH,
            ClassType::MIDDLE_SECOND,
            ClassType::SECONDARY_FIRST,
        ];

        foreach ($classTypes as $classType) {
            SchoolClass::updateOrCreate(
                ['type' => $classType->value],
                ['type' => $classType->value]
            );
        }

        $classes = SchoolClass::all();

        foreach ($classes as $schoolClass) {
            foreach (['A', 'B'] as $sectionName) {
                Section::updateOrCreate(
                    ['class_id' => $schoolClass->id, 'name' => $sectionName],
                    [
                        'class_id' => $schoolClass->id,
                        'name' => $sectionName,
                        'capacity' => 30,
                    ]
                );
            }
        }

        return $classes;
    }

    private function createSubjects(): \Illuminate\Database\Eloquent\Collection
    {
        $subjects = [
            ['name' => 'اللغة العربية', 'code' => 'AR', 'description' => 'مادة اللغة العربية'],
            ['name' => 'الرياضيات', 'code' => 'MA', 'description' => 'مادة الرياضيات'],
            ['name' => 'العلوم', 'code' => 'SC', 'description' => 'مادة العلوم'],
            ['name' => 'اللغة الإنجليزية', 'code' => 'EN', 'description' => 'مادة اللغة الإنجليزية'],
        ];

        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['code' => $subject['code']],
                [
                    'name' => $subject['name'],
                    'description' => $subject['description'],
                    'teacher_id' => Teacher::inRandomOrder()->value('id'),
                    'class_id' => SchoolClass::inRandomOrder()->value('id'),
                    'pass_mark' => 50,
                    'full_mark' => 100,
                    'code' => $subject['code'],
                ]
            );
        }

        for ($i = 1; $i <= 6; $i++) {
            $code = 'SUB-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            Subject::updateOrCreate(
                ['code' => $code],
                [
                    'name' => 'مادة تجريبية ' . $i,
                    'description' => 'مادة إضافية رقم ' . $i,
                    'pass_mark' => 50,
                    'full_mark' => 100,
                    'code' => $code,
                    'teacher_id' => Teacher::inRandomOrder()->value('id'),
                    'class_id' => SchoolClass::inRandomOrder()->value('id'),
                ]
            );
        }

        return Subject::all();
    }

    private function createTeachers(Role $teacherRole): \Illuminate\Support\Collection
    {
        $teacherData = [
            ['first' => 'فهد', 'last' => 'العلي', 'specialization' => 'mathematics'],
            ['first' => 'سارة', 'last' => 'الدهام', 'specialization' => 'arabic'],
            ['first' => 'ريم', 'last' => 'الحسان', 'specialization' => 'science'],
            ['first' => 'أحمد', 'last' => 'الطائي', 'specialization' => 'english'],
            ['first' => 'نورة', 'last' => 'السبتي', 'specialization' => 'history'],
        ];

        $teachers = collect([]);

        foreach ($teacherData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => 'teacher' . ($index + 1) . '@example.com'],
                [
                    'name' => $data['first'] . ' ' . $data['last'],
                    'email' => 'teacher' . ($index + 1) . '@example.com',
                    'phone' => '050' . str_pad((string) ($index + 1) . '000000', 7, '0', STR_PAD_LEFT),
                    'role_id' => $teacherRole->id,
                    'is_active' => true,
                    'password' => Hash::make('password'),
                ]
            );

            $teacher = Teacher::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'first_name' => $data['first'],
                    'last_name' => $data['last'],
                    'specialization' => $data['specialization'],
                    'national_id' => 'TCH' . str_pad((string) ($index + 1), 7, '0', STR_PAD_LEFT),
                    'registry_number' => 'REG-TEACH-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'employee_number' => 'EMP-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'hire_date' => Carbon::now()->subYears(2 + $index)->toDateString(),
                    'employment_type' => $index % 2 === 0 ? 'full_time' : 'part_time',
                    'grade' => 'A',
                    'address' => 'الرياض',
                    'phone_alt' => '055' . str_pad((string) ($index + 1), 7, '0', STR_PAD_LEFT),
                    'experience_years' => 3 + $index,
                ]
            );

            $teachers->push($teacher);
        }

        return $teachers;
    }

    private function createSubjectResults(\Illuminate\Support\Collection $students_enrollments, \Illuminate\Support\Collection $subjects): void
    {
        foreach ($students_enrollments as $student_enrollment) {
            foreach ($subjects as $subject) {
                $mark = rand(30, 100);
                $result = $mark >= 50 ? MarkResult::PASS : MarkResult::FAIL;

                StudentSubjectResult::firstOrCreate(
                    [
                        'enrollment_id' => $student_enrollment->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'enrollment_id' => $student_enrollment->id,
                        'subject_id' => $subject->id,
                        'term1_mark' => $mark,
                        'term2_mark' => $mark,
                        'result' => $result->value,
                    ]
                );
            }
        }
    }

    private function createParents(Role $parentRole): \Illuminate\Support\Collection
    {
        $parents = collect([]);

        for ($i = 1; $i <= 10; $i++) {
            $user = User::updateOrCreate(
                ['email' => 'parent' . $i . '@example.com'],
                [
                    'name' => 'ولي أمر ' . $i,
                    'email' => 'parent' . $i . '@example.com',
                    'phone' => '053' . str_pad((string) ($i + 1), 7, '0', STR_PAD_LEFT),
                    'role_id' => $parentRole->id,
                    'is_active' => true,
                    'password' => Hash::make('password'),
                ]
            );

            $parents->push($user);
        }

        return $parents;
    }

    private function createStudents(Role $studentRole, \Illuminate\Support\Collection $parents): \Illuminate\Support\Collection
    {
        $studentData = [
            ['first' => 'محمد', 'last' => 'الزهراني'],
            ['first' => 'ليلى', 'last' => 'العتيبي'],
            ['first' => 'طلال', 'last' => 'الحربي'],
            ['first' => 'هدى', 'last' => 'الشمري'],
            ['first' => 'ياسر', 'last' => 'الراشد'],
            ['first' => 'سعيد', 'last' => 'العيسى'],
            ['first' => 'نور', 'last' => 'الهاجري'],
            ['first' => 'علي', 'last' => 'السرحاني'],
            ['first' => 'سلمى', 'last' => 'البدر'],
            ['first' => 'عمر', 'last' => 'الجعيد'],
        ];

        $students = collect([]);

        foreach ($studentData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => 'student' . ($index + 1) . '@example.com'],
                [
                    'name' => $data['first'] . ' ' . $data['last'],
                    'email' => 'student' . ($index + 1) . '@example.com',
                    'phone' => '054' . str_pad((string) ($index + 1), 7, '0', STR_PAD_LEFT),
                    'role_id' => $studentRole->id,
                    'is_active' => true,
                    'password' => Hash::make('password'),
                ]
            );

            $parent = $parents->get($index % $parents->count());

            $student = Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'parent_id' => $parent->id,
                    'first_name' => $data['first'],
                    'last_name' => $data['last'],
                    'father_name' => 'عبدالله',
                    'mother_name' => 'سارة',
                    'national_id' => 'STU' . str_pad((string) ($index + 1), 7, '0', STR_PAD_LEFT),
                    'registry_number' => 'REG-STU-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'birth_date' => Carbon::now()->subYears(10 + $index)->toDateString(),
                    'gender' => $index % 2 === 0 ? 'male' : 'female',
                ]
            );

            $students->push($student);
        }

        return $students;
    }

    private function createEnrollments(Collection $students, Collection $classes): Collection
    {
        $sections = Section::all();
        $statuses = [
            StudentStatus::ACTIVE,
            StudentStatus::PROMOTED,
            StudentStatus::REPEATED,
            StudentStatus::TRANSFERRED,
            StudentStatus::GRADUATED,
            StudentStatus::WITHDRAWN,
        ];

        $students_enrollments = collect([]);

        foreach ($students as $index => $student) {
            $section = $sections->get($index % $sections->count());
            $status = $statuses[$index % count($statuses)];
            $finalResult = $status === StudentStatus::REPEATED || $status === StudentStatus::WITHDRAWN || $status === StudentStatus::TRANSFERRED ? MarkResult::PENDING : [MarkResult::PASS, MarkResult::FAIL][$index % 2];

            $student_enrollment = StudentEnrollment::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year' => '2025-2026',
                ],
                [
                    'student_id' => $student->id,
                    'section_id' => $section->id,
                    'academic_year' => '2025-2026',
                    'enrollment_date' => Carbon::now()->subMonths(2 + $index),
                    'status' => $status->value,
                    'final_result' => $finalResult,
                    'final_average' => rand(30, 100),
                    'notes' => 'تسجيل تجريبي رقم ' . ($index + 1),
                ]
            );
            $students_enrollments->push($student_enrollment);
        }

        return $students_enrollments;
    }

    private function createStudentAttendances(\Illuminate\Support\Collection $students): void
    {
        $states = [AttendanceStatus::PRESENT, AttendanceStatus::ABSENT, AttendanceStatus::LATE];

        foreach ($students as $index => $student) {
            for ($day = 0; $day < 5; $day++) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'date' => Carbon::today()->subDays($day)->toDateString(),
                    ],
                    [
                        'section_id' => StudentEnrollment::where('student_id', $student->id)->where('academic_year', '2025-2026')->value('section_id'),
                        'status' => $states[($index + $day) % count($states)]->value,
                        'date' => Carbon::today()->subDays($day)->toDateString(),
                    ]
                );
            }
        }
    }

    private function createTeacherAttendances(\Illuminate\Support\Collection $teachers): void
    {
        $states = [AttendanceStatus::PRESENT, AttendanceStatus::ABSENT, AttendanceStatus::LATE];

        foreach ($teachers as $index => $teacher) {
            for ($day = 0; $day < 5; $day++) {
                TeacherAttendance::updateOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'date' => Carbon::today()->subDays($day)->toDateString(),
                    ],
                    [
                        'status' => $states[($index + $day) % count($states)]->value,
                    ]
                );
            }
        }
    }

    private function createCommunicationSamples(User $admin): void
    {
        for ($i = 1; $i <= 6; $i++) {
            Suggestion::updateOrCreate(
                ['title' => 'اقتراح تجريبي ' . $i],
                [
                    'user_id' => $admin->id,
                    'title' => 'اقتراح تجريبي ' . $i,
                    'body' => 'هذا اقتراح تجريبي يستعمله فريق التطوير لعرض ميزة ' . $i . '.',
                    'status' => SuggestionStatus::Pending->value,
                    'feedback' => $i % 2 === 0 ? 'تمت المراجعة' : null,
                ]
            );

            Complaint::updateOrCreate(
                ['title' => 'شكوى تجريبية ' . $i],
                [
                    'user_id' => $admin->id,
                    'title' => 'شكوى تجريبية ' . $i,
                    'body' => 'هذا نص شكوى تجريبية رقم ' . $i . ' للتحقق من لوحة الشكاوى.',
                    'status' => [ComplaintStatus::PENDING, ComplaintStatus::IN_PROGRESS, ComplaintStatus::RESOLVED][($i - 1) % 3]->value,
                    'response' => $i % 3 === 0 ? 'تم حل المشكلة بنجاح.' : null,
                    'resolved_at' => $i % 3 === 0 ? Carbon::now()->subDays($i) : null,
                ]
            );
        }
    }

    private function createNewsSamples(User $admin): void
    {
        for ($i = 1; $i <= 5; $i++) {
            News::updateOrCreate(
                ['title' => 'خبر تعريفي ' . $i],
                [
                    'title' => 'خبر تعريفي ' . $i,
                    'body' => 'هذا خبر تعريفي رقم ' . $i . ' لعرض واجهة الأخبار.',
                    'audience' => [AudienceType::ALL, AudienceType::TEACHERS, AudienceType::PARENTS, AudienceType::STUDENTS][($i - 1) % 4]->value,
                    'created_by' => $admin->id,
                ]
            );
        }
    }

    private function createTimeSlots(): void
    {
        $timeSlots = [
            ['start_time' => '08:00:00', 'end_time' => '09:00:00'],
            ['start_time' => '09:15:00', 'end_time' => '10:15:00'],
            ['start_time' => '10:30:00', 'end_time' => '11:30:00'],
            ['start_time' => '11:45:00', 'end_time' => '12:45:00'],
            ['start_time' => '13:00:00', 'end_time' => '14:00:00'],
        ];

        foreach ($timeSlots as $slot) {
            TimeSlot::updateOrCreate(
                ['start_time' => $slot['start_time'], 'end_time' => $slot['end_time']],
                [
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                ]
            );
        }
    }

    public function createTerms(): void
    {
        $terms = [
            ['type' => TermType::FIRST_TERM->value, 'start_date' => '2025-09-01', 'end_date' => '2025-12-31', 'academic_year' => '2025-2026'],
            ['type' => TermType::SECOND_TERM->value, 'start_date' => '2026-01-01', 'end_date' => '2026-05-31', 'academic_year' => '2026-2027'],
        ];

        foreach ($terms as $term) {
            Term::updateOrCreate(
                ['type' => $term['type']],
                [
                    'type' => $term['type'],
                    'start_date' => $term['start_date'],
                    'end_date' => $term['end_date'],
                    'academic_year' => $term['academic_year'],
                ]
            );
        }
    }

    public function createSchedules(Collection $teachers, Collection $subjects, Collection $classes): void
    {
        Schedule::updateOrCreate(
            [
                'section_id' => Section::inRandomOrder()->value('id'),
                'subject_id' => Subject::inRandomOrder()->value('id'),
                'term_id' => Term::inRandomOrder()->value('id'),
                'day' => DayOfWeek::MONDAY,
                'time_slot_id' => TimeSlot::inRandomOrder()->value('id'),
            ],
            [
                'section_id' => Section::inRandomOrder()->value('id'),
                'subject_id' => Subject::inRandomOrder()->value('id'),
                'term_id' => Term::inRandomOrder()->value('id'),
                'day' => DayOfWeek::SUNDAY,
                'time_slot_id' => TimeSlot::inRandomOrder()->value('id'),
            ]
        );
    }
}
