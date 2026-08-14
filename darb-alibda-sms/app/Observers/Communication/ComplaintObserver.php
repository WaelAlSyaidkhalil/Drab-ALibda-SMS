<?php

namespace App\Observers\Communication;

use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Auth\User;
use App\Models\Communication\Complaint;
use App\Notifications\Admin\ComplaintStatusUpdatedNotification;
use App\Notifications\Admin\ComplaintSubmittedNotification;

class ComplaintObserver
{
    public function created(Complaint $complaint): void
    {
        $admins = User::query()->whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $admin->notifyNow(new ComplaintSubmittedNotification($complaint));
        }
    }

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
