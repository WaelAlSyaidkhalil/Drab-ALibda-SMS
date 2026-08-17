<?php

namespace Tests\Unit;

use App\Models\Academic\Student;
use App\Models\Auth\User;
use App\Models\Communication\AbsenceJustification;
use App\Notifications\Admin\AbsenceJustificationStatusUpdatedNotification;
use PHPUnit\Framework\TestCase;

class AbsenceJustificationStatusUpdatedNotificationTest extends TestCase
{
    public function test_notification_structure_and_title_body(): void
    {
        $admin = new User([
            'id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $student = new Student([
            'id' => 10,
            'first_name' => 'سامي',
            'last_name' => 'أحمد',
        ]);

        $justification = new AbsenceJustification([
            'id' => 100,
            'student_id' => 10,
            'parent_id' => 5,
            'status' => 'approved',
            'review_note' => 'تم التبرير بنجاح',
        ]);

        $justification->setRelation('student', $student);

        $notification = new AbsenceJustificationStatusUpdatedNotification($justification, $admin);

        $this->assertSame('تم تحديث حالة تبرير الغياب', $notification->title());
        $this->assertStringContainsString('سامي أحمد', $notification->body());
        $this->assertStringContainsString('مقبول', $notification->body());

        $payload = $notification->toDatabase(new User(['id' => 5]));

        $this->assertSame('admin', $payload['from']);
        $this->assertSame(1, $payload['admin_id']);
        $this->assertSame('Admin User', $payload['admin_name']);
        $this->assertSame(100, $payload['absence_justification_id']);
        $this->assertSame(10, $payload['student_id']);
        $this->assertSame('سامي أحمد', $payload['student_name']);
        $this->assertSame('approved', $payload['status']);
        $this->assertSame('absence_justification_status_updated', $payload['type']);
        $this->assertContains('database', $notification->via(new User()));
        $this->assertContains('fcm', $notification->via(new User()));
    }
}
