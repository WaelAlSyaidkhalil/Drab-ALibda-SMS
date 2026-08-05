<?php

namespace App\Notifications\Admin;

use App\Models\Communication\Suggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SuggestionStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Suggestion $suggestion)
    {
    }

    public function title(): string
    {
        return __('dashboard.notifications.suggestion_status_updated_title');
    }

    public function body(): string
    {
        return __('dashboard.notifications.suggestion_status_updated_body', [
            'title' => $this->suggestion->title,
            'status' => $this->suggestion->status->label(),
        ]);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'admin',
            'title' => $this->title(),
            'body' => $this->body(),
            'suggestion_id' => $this->suggestion->id,
            'suggestion_title' => $this->suggestion->title,
            'suggestion_status' => $this->suggestion->status->value,
            'suggestion_status_label' => $this->suggestion->status->label(),
            'type' => 'suggestion_status_updated',
        ];
    }
}
