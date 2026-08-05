<?php

namespace App\Observers\Communication;

use App\Models\Communication\Complaint;
use App\Notifications\Admin\ComplaintStatusUpdatedNotification;
use App\Services\FirebaseService;

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
