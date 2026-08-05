<?php

namespace App\Observers\Academic;

use App\Models\Academic\StudentEnrollment;
use App\Notifications\Admin\StudentEnrolledNotification;
use App\Notifications\Admin\StudentEnrollmentStatusUpdatedNotification;
use App\Services\FirebaseService;

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
        $studentUser->notify($notification);

        if (! empty($studentUser->fcm_token)) {
            app(FirebaseService::class)->sendPushNotification(
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
        if (! $enrollment->wasChanged('status') && ! $enrollment->wasChanged('final_result')) {
            return;
        }

        $studentUser = $enrollment->student?->user;

        if ($studentUser === null) {
            return;
        }

        $notification = new StudentEnrollmentStatusUpdatedNotification($enrollment);
        $studentUser->notify($notification);

        if (! empty($studentUser->fcm_token)) {
            app(FirebaseService::class)->sendPushNotification(
                [$studentUser->fcm_token],
                $notification->title(),
                $notification->body()
            );
        }
    }
}
