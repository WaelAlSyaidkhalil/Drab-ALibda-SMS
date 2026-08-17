<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use App\Enums\DayOfWeek;
use App\Enums\MarkResult;
use App\Enums\SubjectComponentType;
use App\Enums\TermType;
use App\Models\Communication\Complaint;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Student;
use App\Models\Academic\StudentEnrollment;
use App\Models\Academic\Teacher;
use App\Models\Auth\User;
use App\Models\Communication\AbsenceJustification;
use App\Models\Communication\Conversation;
use App\Models\Communication\Message;
use App\Models\Communication\News;
use App\Models\Communication\Suggestion;
use App\Models\Grading\StudentMark;
use App\Models\Grading\StudentSubjectResult;
use App\Models\Schedule\Attendance;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Subject;
use App\Models\Subjects\SubjectComponent;
use App\Models\Subjects\Term;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TeacherDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherUser = User::firstWhere('email', 'teacher1@example.com');

        $teacher = $teacherUser
            ? Teacher::firstWhere('user_id', $teacherUser->id)
            : null;

        $student1 = Student::whereHas(
            'user',
            fn ($query) => $query->where('email', 'student1@example.com')
        )->first();

        $student2 = Student::whereHas(
            'user',
            fn ($query) => $query->where('email', 'student2@example.com')
        )->first();

        $parent1 = User::firstWhere('email', 'parent1@example.com');
        $parent2 = User::firstWhere('email', 'parent2@example.com');

        if (
            ! $teacherUser ||
            ! $teacher ||
            ! $student1 ||
            ! $student2 ||
            ! $parent1 ||
            ! $parent2
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $primaryFirstClass = SchoolClass::firstOrCreate(
            ['type' => ClassType::PRIMARY_FIRST->value],
            ['type' => ClassType::PRIMARY_FIRST->value]
        );

        $primarySecondClass = SchoolClass::firstOrCreate(
            ['type' => ClassType::PRIMARY_SECOND->value],
            ['type' => ClassType::PRIMARY_SECOND->value]
        );

        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        $section1 = Section::firstOrCreate(
            [
                'class_id' => $primaryFirstClass->id,
                'name' => 'أ',
            ],
            [
                'capacity' => 30,
            ]
        );

        $section2 = Section::firstOrCreate(
            [
                'class_id' => $primarySecondClass->id,
                'name' => 'أ',
            ],
            [
                'capacity' => 30,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Academic Term
        |--------------------------------------------------------------------------
        */

        $term = Term::firstOrCreate(
            [
                'type' => TermType::FIRST_TERM->value,
                'academic_year' => '2025-2026',
            ],
            [
                'type' => TermType::FIRST_TERM->value,
                'academic_year' => '2025-2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Subjects
        |--------------------------------------------------------------------------
        */

        $subject1 = Subject::firstOrCreate(
            ['code' => 'MAT101'],
            [
                'name' => 'الرياضيات',
                'description' => 'مادة الرياضيات للصف الأول الابتدائي',
                'full_mark' => 100,
                'pass_mark' => 50,
            ]
        );

        $subject2 = Subject::firstOrCreate(
            ['code' => 'ARAB101'],
            [
                'name' => 'اللغة العربية',
                'description' => 'مادة اللغة العربية للصف الأول الابتدائي',
                'full_mark' => 100,
                'pass_mark' => 50,
            ]
        );

        $subject3 = Subject::firstOrCreate(
            ['code' => 'SCI101'],
            [
                'name' => 'العلوم',
                'description' => 'مادة العلوم للصف الأول الابتدائي',
                'full_mark' => 100,
                'pass_mark' => 50,
            ]
        );

        $subject4 = Subject::firstOrCreate(
            ['code' => 'ENG101'],
            [
                'name' => 'اللغة الإنجليزية',
                'description' => 'مادة اللغة الإنجليزية للصف الأول الابتدائي',
                'full_mark' => 100,
                'pass_mark' => 50,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Subject Components
        |--------------------------------------------------------------------------
        |
        | توزيع الدرجة:
        |
        | Written  = 50
        | Oral     = 30
        | Practical/Assignments = 20
        |
        | Total = 100
        |
        */

        $writtenComponent = $subject1->components()->updateOrCreate(
            [
                'type' => SubjectComponentType::WRITTEN->value,
            ],
            [
                'type' => SubjectComponentType::WRITTEN->value,
                'out_of' => 50,
                'order' => 1,
                'description' => 'الاختبار الكتابي',
            ]
        );

        $oralComponent = $subject1->components()->updateOrCreate(
            [
                'type' => SubjectComponentType::ORAL->value,
            ],
            [
                'type' => SubjectComponentType::ORAL->value,
                'out_of' => 30,
                'order' => 2,
                'description' => 'الاختبار الشفهي',
            ]
        );

        /*
         * نستخدم PRACTICAL حاليًا كمكوّن ثالث
         * بقيمة 20، ونسميه في الوصف "الوظائف".
         *
         * إذا كان لديك HOMEWORK / ASSIGNMENT في Enum
         * فمن الأفضل استبداله بها.
         */
        $homeworkComponent = $subject1->components()->updateOrCreate(
            [
                'type' => SubjectComponentType::PRACTICAL->value,
            ],
            [
                'type' => SubjectComponentType::PRACTICAL->value,
                'out_of' => 20,
                'order' => 3,
                'description' => 'الوظائف والواجبات',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Time Slots
        |--------------------------------------------------------------------------
        */

        $timeSlot1 = TimeSlot::firstOrCreate(
            [
                'start_time' => '08:00:00',
                'end_time' => '08:45:00',
            ]
        );

        $timeSlot2 = TimeSlot::firstOrCreate(
            [
                'start_time' => '08:45:00',
                'end_time' => '09:30:00',
            ]
        );

        $timeSlot3 = TimeSlot::firstOrCreate(
            [
                'start_time' => '09:30:00',
                'end_time' => '10:15:00',
            ]
        );

        $timeSlot4 = TimeSlot::firstOrCreate(
            [
                'start_time' => '10:15:00',
                'end_time' => '11:00:00',
            ]
        );

        $timeSlot5 = TimeSlot::firstOrCreate(
            [
                'start_time' => '11:00:00',
                'end_time' => '11:45:00',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Schedule
        |--------------------------------------------------------------------------
        */

        Schedule::firstOrCreate(
            [
                'section_id' => $section1->id,
                'subject_id' => $subject1->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot1->id,
                'day' => DayOfWeek::MONDAY->value,
            ]
        );

        Schedule::firstOrCreate(
            [
                'section_id' => $section1->id,
                'subject_id' => $subject2->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot2->id,
                'day' => DayOfWeek::TUESDAY->value,
            ]
        );

        Schedule::firstOrCreate(
            [
                'section_id' => $section1->id,
                'subject_id' => $subject3->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot3->id,
                'day' => DayOfWeek::WEDNESDAY->value,
            ]
        );

        Schedule::firstOrCreate(
            [
                'section_id' => $section1->id,
                'subject_id' => $subject4->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot4->id,
                'day' => DayOfWeek::THURSDAY->value,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Student Enrollments
        |--------------------------------------------------------------------------
        */

        $enrollment1 = StudentEnrollment::updateOrCreate(
            [
                'student_id' => $student1->id,
                'academic_year' => '2025-2026',
            ],
            [
                'section_id' => $section1->id,
                'enrollment_date' => '2025-09-01',
                'status' => 'active',
                'final_result' => 'pending',
            ]
        );

        $enrollment2 = StudentEnrollment::updateOrCreate(
            [
                'student_id' => $student2->id,
                'academic_year' => '2025-2026',
            ],
            [
                'section_id' => $section1->id,
                'enrollment_date' => '2025-09-01',
                'status' => 'active',
                'final_result' => 'pending',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Student Subject Result
        |--------------------------------------------------------------------------
        |
        | العلامة النهائية للمادة = مجموع المكونات.
        |
        | مثال الطالب الأول:
        |
        | كتابي  = 40 / 50
        | شفهي   = 25 / 30
        | وظائف  = 18 / 20
        |
        | المجموع = 83 / 100
        |
        */

        $writtenMark = 40;
        $oralMark = 25;
        $homeworkMark = 18;

        $totalMark = $writtenMark + $oralMark + $homeworkMark;

        StudentSubjectResult::updateOrCreate(
            [
                'subject_id' => $subject1->id,
                'enrollment_id' => $enrollment1->id,
            ],
            [
                'subject_id' => $subject1->id,
                'enrollment_id' => $enrollment1->id,
                'term1_mark' => $totalMark,
                'term2_mark' => null,
                'result' => $totalMark >= $subject1->pass_mark
                    ? MarkResult::PASS->value
                    : MarkResult::FAIL->value,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Student Marks
        |--------------------------------------------------------------------------
        |
        | كل مكوّن له سجل مستقل.
        |
        | enrollment_id
        | subject_id
        | subject_component_id
        | term_id
        | mark
        |
        */

        StudentMark::updateOrCreate(
            [
                'enrollment_id' => $enrollment1->id,
                'subject_id' => $subject1->id,
                'subject_component_id' => $writtenComponent->id,
                'term_id' => $term->id,
            ],
            [
                'mark' => $writtenMark,
            ]
        );

        StudentMark::updateOrCreate(
            [
                'enrollment_id' => $enrollment1->id,
                'subject_id' => $subject1->id,
                'subject_component_id' => $oralComponent->id,
                'term_id' => $term->id,
            ],
            [
                'mark' => $oralMark,
            ]
        );

        StudentMark::updateOrCreate(
            [
                'enrollment_id' => $enrollment1->id,
                'subject_id' => $subject1->id,
                'subject_component_id' => $homeworkComponent->id,
                'term_id' => $term->id,
            ],
            [
                'mark' => $homeworkMark,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Attendance
        |--------------------------------------------------------------------------
        */

        Attendance::updateOrCreate(
            [
                'student_id' => $student1->id,
                'section_id' => $section1->id,
                'date' => Carbon::today()->toDateString(),
            ],
            [
                'status' => 'present',
            ]
        );

        Attendance::updateOrCreate(
            [
                'student_id' => $student2->id,
                'section_id' => $section2->id,
                'date' => Carbon::today()->toDateString(),
            ],
            [
                'status' => 'absent',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Absence Justification
        |--------------------------------------------------------------------------
        */

        AbsenceJustification::updateOrCreate(
            [
                'student_id' => $student2->id,
                'parent_id' => $parent2->id,
                'absence_date' => Carbon::today()
                    ->subDay()
                    ->toDateString(),
            ],
            [
                'reason' => 'تغيب لظروف عائلية',
                'status' => 'pending',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | News
        |--------------------------------------------------------------------------
        */

        $news = News::firstOrCreate(
            [
                'title' => 'إعلان اليوم للمعلمين',
            ],
            [
                'body' => 'هذا إعلان تجريبي يظهر اليوم للمعلمين.',
                'audience' => 'teachers',
                'created_by' => $teacherUser->id,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Conversation
        |--------------------------------------------------------------------------
        */

        $conversation = Conversation::firstOrCreate(
            [
                'user1_id' => $teacherUser->id,
                'user2_id' => $parent1->id,
            ]
        );

        Message::firstOrCreate(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $parent1->id,
                'message' => 'هل يمكنك مراجعة حالة حضور الطالب؟',
            ],
            [
                'is_read' => false,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Complaints
        |--------------------------------------------------------------------------
        */

        Complaint::firstOrCreate(
            [
                'user_id' => $parent1->id,
                'title' => 'شكوى تجريبية',
            ],
            [
                'body' => 'هذه شكوى تجريبية تم إنشاؤها بواسطة Seeder.',
                'status' => 'pending',
            ]
        );

        Complaint::firstOrCreate(
            [
                'user_id' => $parent1->id,
                'title' => 'شكوى أخرى',
            ],
            [
                'body' => 'هذه شكوى أخرى تم إنشاؤها بواسطة Seeder.',
                'status' => 'in_progress',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Suggestions
        |--------------------------------------------------------------------------
        */

        Suggestion::firstOrCreate(
            [
                'user_id' => $parent2->id,
                'title' => 'اقتراح تجريبي',
            ],
            [
                'body' => 'هذا اقتراح تجريبي تم إنشاؤه بواسطة Seeder.',
                'is_acknowledged' => false,
            ]
        );

        Suggestion::firstOrCreate(
            [
                'user_id' => $parent2->id,
                'title' => 'اقتراح آخر',
            ],
            [
                'body' => 'هذا اقتراح آخر تم إنشاؤه بواسطة Seeder.',
                'is_acknowledged' => true,
            ]
        );
    }
}