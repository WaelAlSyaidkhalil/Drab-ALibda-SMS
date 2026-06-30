<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use App\Enums\DayOfWeek;
use App\Enums\MarkResult;
use App\Enums\StudentStatus;
use App\Enums\SubjectComponentType;
use App\Enums\TermType;
use App\Enums\TimeSlotNumber;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Student;
use App\Models\Academic\StudentEnrollment;
use App\Models\Academic\Teacher;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Subject;
use App\Models\Subjects\SubjectComponent;
use App\Models\Subjects\Term;
use App\Services\Admin\GeneratePasswordService;
use Illuminate\Database\Seeder;

class AcademicGradingSeeder extends Seeder
{
    public function run(GeneratePasswordService $generatePasswordService): void
    {
        $academicYear = '2025-2026';
        $teacherRole = Role::firstWhere('name', 'teacher');

        $this->createTimeSlots();
        $terms = $this->createTerms($academicYear); // سنستخدم الفصل الأول فقط
        $classes = $this->createClassesAndSections(); // 6 صفوف × 3 شعب
        $subjects = $this->createSubjectsAndComponents();
        $this->attachSubjectsToClasses($classes, $subjects);
        $this->attachSubjectsToTerms($terms, $subjects);

        // تحديد مادة الإنجليزي
        $englishSubject = $subjects->firstWhere('code', 'ENG');

        // إنشاء معلم إنجليزي واحد لكل المدرسة
        $englishTeacher = $this->createEnglishTeacher($englishSubject, $teacherRole, $generatePasswordService);

        // إنشاء معلم خاص لكل شعبة (هوم روم) يدرّس كل المواد عدا الإنجليزي
        $sectionTeacherMap = $this->createHomeroomTeachersForSections($teacherRole, $generatePasswordService);

        // إنشاء الجداول
        $this->createSchedules($terms, $subjects, $englishSubject, $englishTeacher, $sectionTeacherMap);

        // تسجيل الطلاب في الشعب
        $this->createStudentEnrollments($academicYear);
    }

    protected function createTimeSlots(): void
    {
        foreach (TimeSlotNumber::cases() as $slotNumber) {
            TimeSlot::updateOrCreate(
                ['period_number' => $slotNumber->value],
                [
                    'start_time' => match ($slotNumber) {
                        TimeSlotNumber::FIRST => '08:00:00',
                        TimeSlotNumber::SECOND => '08:45:00',
                        TimeSlotNumber::THIRD => '09:30:00',
                        TimeSlotNumber::FOURTH => '10:15:00',
                        TimeSlotNumber::FIFTH => '11:00:00',
                        TimeSlotNumber::SIXTH => '11:45:00',
                        TimeSlotNumber::SEVENTH => '12:30:00',
                    },
                    'end_time' => match ($slotNumber) {
                        TimeSlotNumber::FIRST => '08:45:00',
                        TimeSlotNumber::SECOND => '09:30:00',
                        TimeSlotNumber::THIRD => '10:15:00',
                        TimeSlotNumber::FOURTH => '11:00:00',
                        TimeSlotNumber::FIFTH => '11:45:00',
                        TimeSlotNumber::SIXTH => '12:30:00',
                        TimeSlotNumber::SEVENTH => '13:15:00',
                    },
                ]
            );
        }
    }

    protected function createTerms(string $academicYear)
    {
        // فصل دراسي واحد فقط: الأول
        $term = Term::updateOrCreate(
            ['type' => TermType::FIRST_TERM->value, 'academic_year' => $academicYear],
            [
                'type' => TermType::FIRST_TERM->value,
                'academic_year' => $academicYear,
                'start_date' => '2025-09-01',
                'end_date' => '2025-12-31',
            ]
        );

        return collect([$term]);
    }

    protected function createClassesAndSections()
    {
        // نستخدم أول 6 صفوف فقط من الـ Enum
        $classes = collect();
        $classTypes = collect(ClassType::cases())->take(6);

        foreach ($classTypes as $classType) {
            $schoolClass = SchoolClass::updateOrCreate(
                ['type' => $classType->value],
                ['type' => $classType->value]
            );

            foreach (['A', 'B', 'C'] as $sectionName) {
                Section::updateOrCreate(
                    ['class_id' => $schoolClass->id, 'name' => $sectionName],
                    ['class_id' => $schoolClass->id, 'name' => $sectionName, 'capacity' => 35]
                );
            }

            $classes->push($schoolClass);
        }

        return $classes;
    }

    protected function createSubjectsAndComponents()
    {
        $subjectsData = [
            ['name' => 'الرياضيات', 'code' => 'MAT', 'description' => 'مادة رياضيات أساسية'],
            ['name' => 'اللغة العربية', 'code' => 'ARA', 'description' => 'مادة عربية أساسية'],
            ['name' => 'اللغة الإنجليزية', 'code' => 'ENG', 'description' => 'مادة إنجليزية أساسية'],
            ['name' => 'العلوم', 'code' => 'SCI', 'description' => 'مادة علوم أساسية'],
            ['name' => 'التاريخ', 'code' => 'HIS', 'description' => 'مادة تاريخ'],
            ['name' => 'الجغرافيا', 'code' => 'GEO', 'description' => 'مادة جغرافيا'],
            ['name' => 'العلوم الإسلامية', 'code' => 'ISL', 'description' => 'مادة علوم إسلامية'],
            ['name' => 'الحاسوب', 'code' => 'COM', 'description' => 'مادة حاسوب'],
        ];

        $subjects = collect();

        foreach ($subjectsData as $subjectData) {
            $subject = Subject::updateOrCreate(
                ['code' => $subjectData['code']],
                [
                    'name' => $subjectData['name'],
                    'description' => $subjectData['description'],
                    'pass_mark' => 50,
                    'full_mark' => 100,
                    'code' => $subjectData['code'],
                ]
            );

            $components = [
                ['type' => SubjectComponentType::WRITTEN->value, 'out_of' => 20, 'order' => 1],
                ['type' => SubjectComponentType::ORAL->value, 'out_of' => 20, 'order' => 2],
            ];

            if (in_array($subjectData['code'], ['SCI', 'COM'])) {
                $components[] = ['type' => SubjectComponentType::PRACTICAL->value, 'out_of' => 20, 'order' => 3];
            }

            foreach ($components as $componentData) {
                SubjectComponent::updateOrCreate(
                    ['subject_id' => $subject->id, 'type' => $componentData['type']],
                    [
                        'subject_id' => $subject->id,
                        'type' => $componentData['type'],
                        'out_of' => $componentData['out_of'],
                        'order' => $componentData['order'],
                        'description' => $componentData['type'] === SubjectComponentType::WRITTEN->value
                            ? 'اختبار تحريري'
                            : ($componentData['type'] === SubjectComponentType::ORAL->value
                                ? 'اختبار شفهي'
                                : 'اختبار عملي'),
                    ]
                );
            }

            $subjects->push($subject);
        }

        return $subjects;
    }

    protected function attachSubjectsToClasses($classes, $subjects): void
    {
        foreach ($classes as $schoolClass) {
            foreach ($subjects as $subject) {
                $schoolClass->subjects()->syncWithoutDetaching([$subject->id]);
            }
        }
    }

    protected function attachSubjectsToTerms($terms, $subjects): void
    {
        foreach ($terms as $term) {
            foreach ($subjects as $subject) {
                $term->subjects()->syncWithoutDetaching([$subject->id]);
            }
        }
    }

    protected function createEnglishTeacher(Subject $englishSubject, ?Role $teacherRole, GeneratePasswordService $generatePasswordService): Teacher
    {
        $email = 'english@school.test';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'معلم اللغة الإنجليزية',
                'email' => $email,
                'phone' => '05' . rand(10000000, 99999999),
                'role_id' => $teacherRole?->id,
                'password' => $generatePasswordService->generatePassword(),
            ]
        );

        return Teacher::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => 'معلم',
                'last_name' => 'اللغة الإنجليزية',
                'gender' => 'male',
                'national_id' => 'ENG' . rand(1000000000, 9999999999),
                'registry_number' => 'REG-ENG',
                'specialization' => 'english',
                'employee_number' => 'EMP-ENG',
                'hire_date' => '2020-09-01',
                'employment_type' => 'full_time',
                'grade' => 'A',
                'address' => 'الرياض',
                'phone_alt' => '05' . rand(10000000, 99999999),
            ]
        );
    }

    protected function createHomeroomTeachersForSections(?Role $teacherRole, GeneratePasswordService $generatePasswordService)
    {
        $sections = Section::with('schoolClass')->orderBy('class_id')->orderBy('name')->get();
        $map = [];

        foreach ($sections as $section) {
            $email = "teacher_section_{$section->id}@school.test";

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'معلم شعبة ' . ($section->schoolClass->type instanceof ClassType ? $section->schoolClass->type->value : $section->schoolClass->type) . '-' . $section->name,
                    'email' => $email,
                    'phone' => '05' . rand(10000000, 99999999),
                    'role_id' => $teacherRole?->id,
                    'password' => $generatePasswordService->generatePassword(),
                ]
            );

            $teacher = Teacher::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'first_name' => 'معلم',
                    'last_name' => 'شعبة ' . ($section->schoolClass->type instanceof ClassType ? $section->schoolClass->type->value : $section->schoolClass->type) . '-' . $section->name,
                    'gender' => 'male',
                    'national_id' => 'T' . rand(1000000000, 9999999999),
                    'registry_number' => 'REG-SEC-' . $section->id,
                    'specialization' => 'general',
                    'employee_number' => 'EMP-SEC-' . $section->id,
                    'hire_date' => '2020-09-01',
                    'employment_type' => 'full_time',
                    'grade' => 'A',
                    'address' => 'الرياض',
                    'phone_alt' => '05' . rand(10000000, 99999999),
                ]
            );

            $map[$section->id] = $teacher;
        }

        return $map;
    }

    protected function createSchedules($terms, $subjects, Subject $englishSubject, Teacher $englishTeacher, array $sectionTeacherMap): void
    {
        $sections = Section::with('schoolClass')->orderBy('class_id')->orderBy('name')->get();
        $days = DayOfWeek::cases();
        $timeSlots = TimeSlot::query()->orderBy('period_number')->get();

        $term = $terms->first(); // فصل واحد فقط

        foreach ($sections as $section) {
            $sectionTeacher = $sectionTeacherMap[$section->id] ?? null;
            if (! $sectionTeacher) {
                continue;
            }

            $sectionSlots = [];
            $englishTeacherSlots = [];
            $sectionTeacherSlots = [];

            $englishSlotFound = false;

            foreach ($days as $day) {
                foreach ($timeSlots as $timeSlot) {
                    $slotKey = $day->value . ':' . $timeSlot->id;

                    $sectionTaken = isset($sectionSlots[$slotKey])
                        || Schedule::query()
                            ->where('section_id', $section->id)
                            ->where('term_id', $term->id)
                            ->where('day', $day->value)
                            ->where('time_slot_id', $timeSlot->id)
                            ->exists();
                    $teacherTaken = isset($englishTeacherSlots[$slotKey])
                        || Schedule::query()
                            ->where('teacher_id', $englishTeacher->id)
                            ->where('term_id', $term->id)
                            ->where('day', $day->value)
                            ->where('time_slot_id', $timeSlot->id)
                            ->exists();

                    if ($sectionTaken || $teacherTaken) {
                        continue;
                    }

                    Schedule::create([
                        'section_id' => $section->id,
                        'subject_id' => $englishSubject->id,
                        'teacher_id' => $englishTeacher->id,
                        'term_id' => $term->id,
                        'time_slot_id' => $timeSlot->id,
                        'day' => $day->value,
                    ]);

                    $sectionSlots[$slotKey] = true;
                    $englishTeacherSlots[$slotKey] = true;

                    $englishSlotFound = true;
                    break 2;
                }
            }

            if (! $englishSlotFound) {
                $this->command->warn("Could not assign English slot for section {$section->id}.");
            }

            foreach ($subjects as $subject) {
                if ($subject->id === $englishSubject->id) {
                    continue;
                }

                if (! $section->schoolClass->subjects()->whereKey($subject->id)->exists()) {
                    continue;
                }

                $slotFound = false;

                foreach ($days as $day) {
                    foreach ($timeSlots as $timeSlot) {
                        $slotKey = $day->value . ':' . $timeSlot->id;

                        $sectionTaken = isset($sectionSlots[$slotKey])
                            || Schedule::query()
                                ->where('section_id', $section->id)
                                ->where('term_id', $term->id)
                                ->where('day', $day->value)
                                ->where('time_slot_id', $timeSlot->id)
                                ->exists();
                        $teacherTaken = isset($sectionTeacherSlots[$slotKey])
                            || Schedule::query()
                                ->where('teacher_id', $sectionTeacher->id)
                                ->where('term_id', $term->id)
                                ->where('day', $day->value)
                                ->where('time_slot_id', $timeSlot->id)
                                ->exists();

                        if ($sectionTaken || $teacherTaken) {
                            continue;
                        }

                        Schedule::create([
                            'section_id' => $section->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $sectionTeacher->id,
                            'term_id' => $term->id,
                            'time_slot_id' => $timeSlot->id,
                            'day' => $day->value,
                        ]);

                        $sectionSlots[$slotKey] = true;
                        $sectionTeacherSlots[$slotKey] = true;

                        $slotFound = true;
                        break 2;
                    }
                }

                if (! $slotFound) {
                    $this->command->warn("Skipping schedule for section {$section->id}, subject {$subject->id} because no available slot was found.");
                }
            }
        }
    }

    protected function createStudentEnrollments(string $academicYear): void
    {
        $students = Student::query()->get();
        $sections = Section::query()->orderBy('class_id')->orderBy('name')->get();

        if ($students->isEmpty() || $sections->isEmpty()) {
            return;
        }

        foreach ($students as $index => $student) {
            $section = $sections->get($index % $sections->count());

            if (! $section) {
                continue;
            }

            StudentEnrollment::updateOrCreate(
                ['student_id' => $student->id, 'academic_year' => $academicYear],
                [
                    'student_id' => $student->id,
                    'section_id' => $section->id,
                    'academic_year' => $academicYear,
                    'enrollment_date' => now()->subDays(10),
                    'status' => StudentStatus::ACTIVE->value,
                    'final_result' => MarkResult::PENDING->value,
                    'final_average' => null,
                    'notes' => 'تم إنشاؤه عبر AcademicGradingSeeder',
                ]
            );
        }
    }
}
