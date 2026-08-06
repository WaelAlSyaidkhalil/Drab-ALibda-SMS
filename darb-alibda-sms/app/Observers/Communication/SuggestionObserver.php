<?php

namespace App\Observers\Communication;

use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Communication\Suggestion;
use App\Notifications\Admin\SuggestionStatusUpdatedNotification;

class SuggestionObserver
{
    public function updated(Suggestion $suggestion): void
    {
        if (! $suggestion->wasChanged('status')) {
            return;
        }

        $user = $suggestion->user;
        if ($user === null) {
            return;
        }

        $notification = new SuggestionStatusUpdatedNotification($suggestion);
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
