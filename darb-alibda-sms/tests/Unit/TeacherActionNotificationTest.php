<?php

namespace Tests\Unit;

use App\Models\Academic\Student;
use App\Models\Auth\User;
use App\Notifications\Parent\TeacherActionNotification;
use PHPUnit\Framework\TestCase;

class TeacherActionNotificationTest extends TestCase
{
    public function test_notification_contains_student_full_name_and_parent_source(): void
    {
        $teacher = new User(['name' => 'أحمد المعلم']);
        $student = new Student([
            'id' => 55,
            'first_name' => 'سعد',
            'father_name' => 'علي',
            'last_name' => 'الحميدي',
        ]);

        $notification = new TeacherActionNotification(
            $teacher,
            $student,
            'ملاحظة جديدة',
            'تم إرسال ملاحظة جديدة للطالب سعد علي الحميدي.',
            ['type' => 'note']
        );

        $payload = $notification->toDatabase($teacher);

        $this->assertSame('teacher', $payload['from']);
        $this->assertSame('سعد علي الحميدي', $payload['student_name']);
        $this->assertStringContainsString('سعد علي الحميدي', $payload['body']);
    }
}
