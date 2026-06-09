<?php

namespace App\Listeners\Teacher;

use App\Events\Teacher\TeacherLoggedIn;
use App\Notifications\Teacher\LoginNotification;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class SendLoginNotification
{
    public function __construct(protected FirebaseService $firebaseService)
    {
    }

    public function handle(TeacherLoggedIn $event): void
    {
        $user = $event->user;
        $notification = new LoginNotification($user, $event->ip);

        if (! $user->fcm_token) {
            Log::info('Teacher login notification skipped because no FCM token exists.', [
                'user_id' => $user->id,
            ]);

            return;
        }

        $this->firebaseService->sendPushNotification(
            $user,
            $notification->title(),
            $notification->body()
        );
    }
}
