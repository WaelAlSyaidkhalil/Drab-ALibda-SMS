<?php

namespace App\Observers\Communication;

use App\Models\Communication\Suggestion;
use App\Notifications\Admin\SuggestionStatusUpdatedNotification;
use App\Services\FirebaseService;

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
