<?php

namespace App\Observers\Academic;

use App\Models\Academic\Teacher;
use App\Notifications\Admin\TeacherCreatedNotification;
use App\Services\FirebaseService;

class TeacherObserver
{
    /**
     * Handle the Teacher "created" event.
     */
    public function created(Teacher $teacher): void
    {
        $user = $teacher->user;

        if ($user === null) {
            return;
        }

        $notification = new TeacherCreatedNotification($teacher);

        $user->notify($notification);

        if (! empty($user->fcm_token)) {
            app(FirebaseService::class)->sendPushNotification(
                [$user->fcm_token],
                $notification->title(),
                $notification->body()
            );
        }
    }
}
