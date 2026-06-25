<?php

namespace Tests\Feature;

use App\Models\Academic\SchoolClass;
use App\Models\Academic\SchoolClass as AcademicSchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Student;
use App\Models\Academic\StudentEnrollment;
use App\Models\Academic\Teacher;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Communication\AbsenceJustification;
use App\Models\Communication\Conversation;
use App\Models\Communication\Message;
use App\Models\Communication\News;
use App\Models\Grading\StudentSubjectResult;
use App\Models\Schedule\Attendance;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\TimeSlot;
use App\Models\Subjects\Subject;
use App\Models\Subjects\Term;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_login_and_fetch_profile(): void
    {
        $parent = $this->createParentUser();

        $response = $this->postJson('/api/parent/login', [
            'phone_number' => $parent->phone,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'phone_number', 'profile_image'],
                    'token',
                    'token_type',
                ],
            ]);

        $token = $response->json('data.token');

        $this->withToken($token)
            ->getJson('/api/parent/profile')
            ->assertOk()
            ->assertJsonPath('data.id', $parent->id)
            ->assertJsonPath('data.name', $parent->name);
    }

    public function test_parent_can_list_and_view_children(): void
    {
        $parent = $this->createParentUser();
        $student = $this->createStudentForParent($parent);

        $token = $this->loginParent($parent);

        $this->withToken($token)
            ->getJson('/api/parent/children')
            ->assertOk()
            ->assertJsonFragment(['full_name' => $student->full_name]);

        $this->withToken($token)
            ->getJson('/api/parent/children/'.$student->id)
            ->assertOk()
            ->assertJsonPath('data.student.id', $student->id);
    }

    public function test_parent_can_view_schedule_grades_and_attendance_for_child(): void
    {
        $parent = $this->createParentUser();
        $student = $this->createStudentForParent($parent);
        $this->seedAcademicData($student);

        $token = $this->loginParent($parent);

        $this->withToken($token)
            ->getJson('/api/parent/children/'.$student->id.'/schedule')
            ->assertOk()
            ->assertJsonStructure(['success', 'message', 'data' => [['day', 'subject', 'teacher', 'start_time', 'end_time']]]);

        $this->withToken($token)
            ->getJson('/api/parent/children/'.$student->id.'/grades')
            ->assertOk()
            ->assertJsonStructure(['success', 'message', 'data' => [['subject', 'exam_type', 'grade', 'max_grade', 'exam_date']]]);

        $this->withToken($token)
            ->getJson('/api/parent/children/'.$student->id.'/attendance')
            ->assertOk()
            ->assertJsonStructure(['success', 'message', 'data' => ['present_days', 'absent_days', 'excused_absences', 'unexcused_absences', 'attendance_percentage']]);
    }

    public function test_parent_can_manage_excuse_requests_and_access_notes_announcements_and_driver(): void
    {
        $parent = $this->createParentUser();
        $student = $this->createStudentForParent($parent);
        $this->seedAcademicData($student);

        $token = $this->loginParent($parent);

        $response = $this->withToken($token)
            ->postJson('/api/parent/excuse-requests', [
                'student_id' => $student->id,
                'absence_date' => '2026-06-20',
                'reason' => 'Medical appointment',
            ]);

        $response->assertCreated();

        $this->withToken($token)
            ->getJson('/api/parent/excuse-requests')
            ->assertOk()
            ->assertJsonStructure(['success', 'message', 'data' => [['id', 'student_id', 'status', 'reason']]]);

        $this->withToken($token)
            ->getJson('/api/parent/notes')
            ->assertOk();

        $this->withToken($token)
            ->getJson('/api/parent/announcements')
            ->assertOk();

        $this->withToken($token)
            ->getJson('/api/parent/children/'.$student->id.'/driver')
            ->assertOk();
    }

    protected function createParentUser(): User
    {
        $role = Role::create(['name' => 'parent', 'description' => 'Parent']);

        return User::create([
            'name' => 'Parent One',
            'email' => 'parent'.uniqid().'@example.com',
            'phone' => '050'.random_int(1000000, 9999999),
            'password' => Hash::make('password123'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function loginParent(User $parent): string
    {
        $response = $this->postJson('/api/parent/login', [
            'phone_number' => $parent->phone,
            'password' => 'password123',
        ]);

        return $response->json('data.token');
    }

    protected function createStudentForParent(User $parent): Student
    {
        $studentUser = User::create([
            'name' => 'Student One',
            'email' => 'student'.uniqid().'@example.com',
            'phone' => '055'.random_int(1000000, 9999999),
            'password' => Hash::make('password123'),
            'role_id' => Role::create(['name' => 'student', 'description' => 'Student'])->id,
            'is_active' => true,
        ]);

        return Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parent->id,
            'first_name' => 'Student',
            'last_name' => 'One',
            'registry_number' => 'REG-'.random_int(1000, 9999),
            'gender' => 'male',
        ]);
    }

    protected function seedAcademicData(Student $student, ?User $parent = null): void
    {
        $parent ??= $student->parent;
        $schoolClass = AcademicSchoolClass::create(['type' => 'primary_first']);
        $section = Section::create(['class_id' => $schoolClass->id, 'name' => 'A', 'capacity' => 30]);
        $term = Term::create([
            'type' => 'First_Term',
            'academic_year' => '2026-2027',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MATH', 'pass_mark' => 50, 'full_mark' => 100]);
        $teacherUser = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher'.uniqid().'@example.com',
            'phone' => '056'.random_int(1000000, 9999999),
            'password' => Hash::make('password123'),
            'role_id' => Role::create(['name' => 'teacher', 'description' => 'Teacher'])->id,
            'is_active' => true,
        ]);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'first_name' => 'Teacher',
            'last_name' => 'One',
            'national_id' => '1234567890',
            'registry_number' => 'T-'.random_int(1000, 9999),
            'gender' => 'male',
        ]);
        $timeSlot = TimeSlot::create(['period_number' => 1, 'start_time' => '08:00:00', 'end_time' => '08:45:00']);

        $enrollment = StudentEnrollment::create([
            'student_id' => $student->id,
            'section_id' => $section->id,
            'academic_year' => '2026-2027',
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
            'final_result' => 'pending',
        ]);

        Schedule::create([
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'term_id' => $term->id,
            'time_slot_id' => $timeSlot->id,
            'day' => 'Mon',
        ]);

        StudentSubjectResult::create([
            'enrollment_id' => $enrollment->id,
            'subject_id' => $subject->id,
            'term1_mark' => 85,
            'term2_mark' => 90,
            'yearly_mark' => 87.5,
            'result' => 'pass',
        ]);

        Attendance::create([
            'schedule_id' => 1,
            'student_id' => $student->id,
            'status' => 'present',
            'date' => '2026-06-01',
        ]);

        Attendance::create([
            'schedule_id' => 1,
            'student_id' => $student->id,
            'status' => 'absent',
            'date' => '2026-06-02',
        ]);

        Attendance::create([
            'schedule_id' => 1,
            'student_id' => $student->id,
            'status' => 'absent',
            'date' => '2026-06-03',
        ]);

        $conversation = Conversation::create(['user1_id' => $teacherUser->id, 'user2_id' => $parent->id]);
        Message::create(['conversation_id' => $conversation->id, 'sender_id' => $teacherUser->id, 'message' => 'Please review the homework plan', 'is_read' => true]);

        News::create([
            'title' => 'School announcement',
            'body' => 'The school will be closed on Friday',
            'audience' => 'parents',
            'created_by' => $teacherUser->id,
        ]);
    }

    public function withToken(string $token, string $type = 'Bearer')
    {
        return $this->withHeader('Authorization', $type.' '.$token);
    }
}
