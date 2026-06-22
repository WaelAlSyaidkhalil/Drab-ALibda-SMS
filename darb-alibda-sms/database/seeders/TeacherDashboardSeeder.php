<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use App\Enums\DayOfWeek;
use App\Enums\MarkResult;
use App\Enums\SubjectComponentType;
use App\Enums\TermType;
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
use App\Models\Grading\StudentMark;
use App\Models\Grading\StudentSubjectResult;
use App\Models\Schedule\Attendance;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Subject;
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
        $teacher = $teacherUser ? Teacher::firstWhere('user_id', $teacherUser->id) : null;
        $student1 = Student::whereHas('user', fn ($query) => $query->where('email', 'student1@example.com'))->first();
        $student2 = Student::whereHas('user', fn ($query) => $query->where('email', 'student2@example.com'))->first();
        $parent1 = User::firstWhere('email', 'parent1@example.com');
        $parent2 = User::firstWhere('email', 'parent2@example.com');

        if (! $teacherUser || ! $teacher || ! $student1 || ! $student2 || ! $parent1 || ! $parent2) {
            return;
        }

        $schoolClass = SchoolClass::firstOrCreate(
            ['type' => ClassType::PRIMARY_FIRST->value],
            ['type' => ClassType::PRIMARY_FIRST->value]
        );

        $section = Section::firstOrCreate(
            [
                'class_id' => $schoolClass->id,
                'name' => 'أ',
            ],
            ['capacity' => 30]
        );

        $term = Term::firstOrCreate(
            ['type' => TermType::FIRST_TERM->value, 'academic_year' => '2025-2026'],
            [
                'type' => TermType::FIRST_TERM->value,
                'academic_year' => '2025-2026',
                'start_date' => '2025-09-01',
                'end_date' => '2026-01-31',
            ]
        );

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

        $subjectComponent = $subject1->components()->firstOrCreate(
            ['type' => SubjectComponentType::WRITTEN->value],
            [
                'type' => SubjectComponentType::WRITTEN->value,
                'out_of' => 70,
                'order' => 1,
            ]
        );

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
        

        $schedule1 = Schedule::firstOrCreate(
            [
                'section_id' => $section->id,
                'subject_id' => $subject1->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot1->id,
                'day' => DayOfWeek::MONDAY->value,
            ]
        );

        $schedule2 = Schedule::firstOrCreate(
            [
                'section_id' => $section->id,
                'subject_id' => $subject2->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot2->id,
                'day' => DayOfWeek::TUESDAY->value,
            ]
        );

        $schedule3 = Schedule::firstOrCreate(
            [
                'section_id' => $section->id,
                'subject_id' => $subject3->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot3->id,
                'day' => DayOfWeek::WEDNESDAY->value,
            ]
        );

        $schedule4 = Schedule::firstOrCreate(
            [
                'section_id' => $section->id,
                'subject_id' => $subject4->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot4->id,
                'day' => DayOfWeek::THURSDAY->value,
            ]
        );

        StudentEnrollment::updateOrCreate(
            ['student_id' => $student1->id, 'academic_year' => '2025-2026'],
            [
                'section_id' => $section->id,
                'enrollment_date' => '2025-09-01',
                'status' => 'active',
                'final_result' => 'pending',
            ]
        );

        StudentEnrollment::updateOrCreate(
            ['student_id' => $student2->id, 'academic_year' => '2025-2026'],
            [
                'section_id' => $section->id,
                'enrollment_date' => '2025-09-01',
                'status' => 'active',
                'final_result' => 'pending',
            ]
        );

        StudentSubjectResult::firstOrCreate(
            ['subject_id' => $subject1->id, 'enrollment_id' => $student1->enrollments()->first()->id],
            [
                'subject_id' => 1,
                'enrollment_id' => 1,
                'term1_mark' => 65,
                'term2_mark' => 70,
                'result' => MarkResult::PASS->value,
            ]
        );

        StudentMark::firstOrCreate(
            [
                'enrollment_id' => 1,
                'subject_component_id' => $subjectComponent->id,
                'subject_id' => $subject1->id,
                'term_id' => $term->id,
            ],
            [
                'mark' => 65,
            ]
        );

        Attendance::updateOrCreate(
            [
                'student_id' => $student1->id,
                'schedule_id' => $schedule1->id,
                'date' => Carbon::today()->toDateString(),
            ],
            ['status' => 'present']
        );

        Attendance::updateOrCreate(
            [
                'student_id' => $student2->id,
                'schedule_id' => $schedule1->id,
                'date' => Carbon::today()->toDateString(),
            ],
            ['status' => 'absent']
        );

        AbsenceJustification::updateOrCreate(
            [
                'student_id' => $student2->id,
                'parent_id' => $parent2->id,
                'absence_date' => Carbon::today()->subDay()->toDateString(),
            ],
            [
                'reason' => 'تغيب لظروف عائلية',
                'status' => 'pending',
            ]
        );

        $news = News::firstOrCreate(
            ['title' => 'إعلان اليوم للمعلمين'],
            [
                'body' => 'هذا إعلان تجريبي يظهر اليوم للمعلمين.',
                'audience' => 'teachers',
                'created_by' => $teacherUser->id,
            ]
        );

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
            ['is_read' => false]
        );
    }
}
