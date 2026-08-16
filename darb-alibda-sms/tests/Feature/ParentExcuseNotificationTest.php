<?php

namespace Tests\Feature;

use App\Models\Academic\Student;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Communication\AbsenceJustification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ParentExcuseNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_absence_excuse_creates_database_notification_for_admins(): void
    {
        $parentRole = Role::create(['name' => 'parent', 'description' => 'Parent']);
        $adminRole = Role::create(['name' => 'admin', 'description' => 'Admin']);

        $parent = User::create([
            'name' => 'Parent One',
            'email' => 'parent@example.com',
            'phone' => '0500000001',
            'password' => 'password123',
            'role_id' => $parentRole->id,
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin One',
            'email' => 'admin@example.com',
            'phone' => '0500000002',
            'password' => 'password123',
            'role_id' => $adminRole->id,
            'is_active' => true,
            'fcm_token' => 'admin-token-1',
        ]);

        $student = Student::create([
            'user_id' => User::create([
                'name' => 'Student One',
                'email' => 'student@example.com',
                'phone' => '0500000004',
                'password' => 'password123',
                'role_id' => Role::create(['name' => 'student', 'description' => 'Student'])->id,
                'is_active' => true,
            ])->id,
            'parent_id' => $parent->id,
            'first_name' => 'Student',
            'last_name' => 'One',
            'registry_number' => 'REG-1001',
            'gender' => 'male',
        ]);

        $token = $this->postJson('/api/parent/login', ['phone_number' => $parent->phone, 'password' => 'password123'])->json('data.token');

        $response = $this->withToken($token)
            ->postJson('/api/parent/excuse-requests', [
                'student_id' => $student->id,
                'absence_date' => '2026-06-20',
                'reason' => 'Medical appointment',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('absence_justifications', [
            'student_id' => $student->id,
            'parent_id' => $parent->id,
            'status' => 'pending',
        ]);

        $justification = AbsenceJustification::query()->where('parent_id', $parent->id)->first();

        $this->assertNotNull($justification);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
        ]);
        $adminNotification = $admin->notifications()->latest()->first();
        $this->assertNotNull($adminNotification);
        $this->assertSame('absence_excuse', data_get($adminNotification->data, 'type'));
        $this->assertSame($justification->id, data_get($adminNotification->data, 'excuse_request_id'));
        $this->assertSame($parent->id, data_get($adminNotification->data, 'parent_id'));
        $this->assertSame($student->id, data_get($adminNotification->data, 'student_id'));
        $this->assertSame($student->full_name, data_get($adminNotification->data, 'student_name'));

        $this->assertTrue($this->app['events']->hasListeners(\Illuminate\Notifications\Events\NotificationSent::class));
    }
}
