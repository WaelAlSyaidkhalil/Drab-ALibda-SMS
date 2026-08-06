<?php

namespace App\Observers\Admin;

use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Communication\News;
use App\Models\User;
use App\Notifications\Admin\NewsCreatedNotification;
use Illuminate\Support\Facades\Storage;

class NewsObserver
{
    /**
     * Handle the News "created" event.
     */
    public function created(News $news): void
    {
        $title = $news->title;
        $body = str($news->body)->stripTags()->limit(120);
        $query = match ($news->audience) {
            'teachers' => User::role('teacher'),
            'students' => User::role('student'),
            'parents'  => User::role('parent'),
            default    => User::query(),
        };

        $users = $query->get();

        foreach ($users as $user) {
            $user->notifyNow(new NewsCreatedNotification($news, $title, $body));
        }

        $tokens = $users
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (! empty($tokens)) {
            SendFirebaseNotificationJob::dispatch($tokens, $title, $body);
        }
    }

    /**
     * Handle the News "updated" event.
     */
    public function updated(News $news): void
    {
        //
    }

    /**
     * Handle the News "deleted" event.
     */
    public function deleted(News $news): void
    {
        foreach ($news->attachments as $attachment) {
            Storage::disk($attachment->disk)->delete($attachment->path);

            $attachment->delete();
        }
    }

    /**
     * Handle the News "restored" event.
     */
    public function restored(News $news): void
    {
        //
    }

    /**
     * Handle the News "force deleted" event.
     */
    public function forceDeleted(News $news): void
    {
        //
    }
}
