<?php

namespace App\Observers\Academic;

use App\Enums\StudentStatus;
use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Academic\StudentEnrollment;
use App\Notifications\Admin\StudentEnrolledNotification;
use App\Notifications\Admin\StudentEnrollmentStatusUpdatedNotification;

class StudentEnrollmentObserver
{
    /**
     * Handle the StudentEnrollment "created" event.
     */
    public function created(StudentEnrollment $enrollment): void
    {
        $studentUser = $enrollment->student?->user;

        if ($studentUser === null) {
            return;
        }

        $notification = new StudentEnrolledNotification($enrollment);
        $studentUser->notifyNow($notification);

        if (! empty($studentUser->fcm_token)) {
            SendFirebaseNotificationJob::dispatch(
                [$studentUser->fcm_token],
                $notification->title(),
                $notification->body()
            );
        }
    }

    /**
     * Handle the StudentEnrollment "updated" event.
     */
    public function updated(StudentEnrollment $enrollment): void
    {
        if (! $enrollment->wasChanged('status')) {
            return;
        }

        $studentUser = $enrollment->student?->user;

        if ($studentUser === null) {
            return;
        }

        $notification = new StudentEnrollmentStatusUpdatedNotification(
            $enrollment->student?->full_name ?? __('dashboard.notifications.student'),
            $enrollment->status->value,
            $enrollment->status->label(),
            $enrollment->final_average,
        );
        $studentUser->notifyNow($notification);

        if (! empty($studentUser->fcm_token)) {
            SendFirebaseNotificationJob::dispatch(
                [$studentUser->fcm_token],
                $notification->title(),
                $notification->body()
            );
        }
    }
}
