<?php

namespace App\Observers\Academic;

use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Academic\Teacher;
use App\Notifications\Admin\TeacherCreatedNotification;

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
        $user->notifyNow($notification);

        if (! empty($user->fcm_token)) {
            SendFirebaseNotificationJob::dispatch(
                [$user->fcm_token],
                $notification->title(),
                $notification->body()
            );
        }
    }
}
