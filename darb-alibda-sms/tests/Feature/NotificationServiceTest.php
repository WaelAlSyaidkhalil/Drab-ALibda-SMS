<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;
    protected function makeUser(array $overrides = []): User
    {
        return User::create([
            'name' => $overrides['name'] ?? 'Test User',
            'email' => $overrides['email'] ?? fake()->unique()->safeEmail(),
            'phone' => $overrides['phone'] ?? fake()->unique()->numerify('##########'),
            'password' => $overrides['password'] ?? 'password',
            'is_active' => $overrides['is_active'] ?? true,
            'fcm_token' => $overrides['fcm_token'] ?? null,
            'role_id' => $overrides['role_id'] ?? null,
        ]);
    }

    public function test_it_sends_a_notification_to_one_user(): void
    {
        Notification::fake();

        $user = $this->makeUser();
        $notification = new class extends \Illuminate\Notifications\Notification
        {
            public function via($notifiable): array
            {
                return ['database'];
            }
        };

        $service = app(NotificationService::class);
        $service->send($user, $notification);

        Notification::assertSentTo($user, get_class($notification));
    }

    public function test_it_sends_a_notification_to_multiple_user_ids(): void
    {
        Notification::fake();

        $users = collect([
            $this->makeUser(['name' => 'A']),
            $this->makeUser(['name' => 'B']),
            $this->makeUser(['name' => 'C']),
        ]);
        $notification = new class extends \Illuminate\Notifications\Notification
        {
            public function via($notifiable): array
            {
                return ['database'];
            }
        };

        $service = app(NotificationService::class);
        $service->sendToUserIds($users->pluck('id')->all(), $notification);

        foreach ($users as $user) {
            Notification::assertSentTo($user, get_class($notification));
        }
    }
}
