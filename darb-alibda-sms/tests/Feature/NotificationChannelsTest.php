<?php

namespace Tests\Feature;

use App\Models\Communication\Suggestion;
use App\Notifications\Admin\SuggestionSubmittedNotification;
use Tests\TestCase;

class NotificationChannelsTest extends TestCase
{
    public function test_suggestion_notification_uses_only_database_channel(): void
    {
        $suggestion = new Suggestion();
        $suggestion->title = 'اقتراح جديد';
        $suggestion->user_id = 1;

        $notification = new SuggestionSubmittedNotification($suggestion);

        $this->assertSame(['database'], $notification->via(new \stdClass()));
    }
}
