<?php

namespace App\Notifications\Admin;

use App\Models\Communication\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewsCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected News $news,
        protected string $title,
        protected string $body
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'admin',
            'title' => $this->title,
            'body' => $this->body,
            'news_id' => $this->news->id,
            'audience' => (string) $this->news->audience,
            'type' => 'news',
        ];
    }
}
