<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use App\Enums\DayOfWeek;
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
                'is_active' => true,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['code' => 'MAT101'],
            [
                'name' => 'الرياضيات',
                'description' => 'مادة الرياضيات للصف الأول الابتدائي',
                'full_mark' => 100,
                'pass_mark' => 50,
            ]
        );

        $timeSlot = TimeSlot::firstOrCreate(
            ['period_number' => 1],
            [
                'start_time' => '08:00:00',
                'end_time' => '08:45:00',
            ]
        );

        $schedule = Schedule::firstOrCreate(
            [
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'term_id' => $term->id,
                'time_slot_id' => $timeSlot->id,
                'day' => DayOfWeek::MONDAY->value,
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

        Attendance::updateOrCreate(
            [
                'student_id' => $student1->id,
                'schedule_id' => $schedule->id,
                'date' => Carbon::today()->toDateString(),
            ],
            ['status' => 'present']
        );

        Attendance::updateOrCreate(
            [
                'student_id' => $student2->id,
                'schedule_id' => $schedule->id,
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
