<?php

namespace App\Observers\Schedule;

use App\Models\Schedule\TeacherAttendance;
use App\Notifications\Admin\TeacherAbsentNotification;
use App\Services\FirebaseService;

class TeacherAttendanceObserver
{
    public function updated(TeacherAttendance $attendance): void
    {
        if (! $attendance->wasChanged('status')) {
            return;
        }

        if ($attendance->status !== 'absent') {
            return;
        }

        $teacherUser = $attendance->teacher?->user;
        if ($teacherUser === null) {
            return;
        }

        $notification = new TeacherAbsentNotification($attendance);
        $teacherUser->notify($notification);

        if (! empty($teacherUser->fcm_token)) {
            app(FirebaseService::class)->sendPushNotification(
                [$teacherUser->fcm_token],
                $notification->title(),
                $notification->body()
            );
        }
    }
}
