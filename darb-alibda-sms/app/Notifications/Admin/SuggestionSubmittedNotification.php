<?php

namespace App\Notifications\Admin;

use App\Models\Communication\Suggestion;
use Illuminate\Notifications\Notification;

class SuggestionSubmittedNotification extends Notification
{
    public function __construct(protected Suggestion $suggestion)
    {
    }

    public function title(): string
    {
        return 'تم إرسال اقتراح جديد';
    }

    public function body(): string
    {
        return 'تم إرسال اقتراح جديد بعنوان: ' . $this->suggestion->title . ' من المستخدم ' . ($this->suggestion->user?->name ?? 'مستخدم');
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'user',
            'title' => $this->title(),
            'body' => $this->body(),
            'suggestion_id' => $this->suggestion->id,
            'suggestion_title' => $this->suggestion->title,
            'user_id' => $this->suggestion->user_id,
            'user_name' => $this->suggestion->user?->name,
            'type' => 'suggestion_submitted',
        ];
    }
}
