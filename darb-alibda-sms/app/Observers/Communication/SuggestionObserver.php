<?php

namespace App\Observers\Communication;

use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Auth\User;
use App\Models\Communication\Suggestion;
use App\Notifications\Admin\SuggestionStatusUpdatedNotification;
use App\Notifications\Admin\SuggestionSubmittedNotification;

class SuggestionObserver
{
    public function created(Suggestion $suggestion): void
    {
        $admins = User::query()->whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            $admin->notifyNow(new SuggestionSubmittedNotification($suggestion));
        }
    }

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
