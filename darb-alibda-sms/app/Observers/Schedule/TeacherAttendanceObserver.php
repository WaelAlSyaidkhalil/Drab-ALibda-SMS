<?php

namespace App\Observers\Schedule;

use App\Enums\AttendanceStatus;
use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Schedule\TeacherAttendance;
use App\Notifications\Admin\TeacherAbsentNotification;

class TeacherAttendanceObserver
{
    public function updated(TeacherAttendance $attendance): void
    {
        if (! $attendance->wasChanged('status')) {
            return;
        }

        if ($attendance->status !== AttendanceStatus::ABSENT) {
            return;
        }
            
        $teacherUser = $attendance->teacher?->user;
        if ($teacherUser === null) {
            return;
        }

        $notification = new TeacherAbsentNotification($attendance);
        $teacherUser->notifyNow($notification);

        if (! empty($teacherUser->fcm_token)) {
            SendFirebaseNotificationJob::dispatch(
                [$teacherUser->fcm_token],
                $notification->title(),
                $notification->body()
            );
        }
    }
}
