<?php

namespace App\Observers\Communication;

use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Communication\Complaint;
use App\Notifications\Admin\ComplaintStatusUpdatedNotification;

class ComplaintObserver
{
    public function updated(Complaint $complaint): void
    {
        if (! $complaint->wasChanged('status')) {
            return;
        }

        $user = $complaint->user;
        if ($user === null) {
            return;
        }

        $notification = new ComplaintStatusUpdatedNotification($complaint);
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
