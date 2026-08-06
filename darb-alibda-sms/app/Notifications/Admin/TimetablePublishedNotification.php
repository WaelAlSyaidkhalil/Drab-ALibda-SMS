<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class TimetablePublishedNotification extends Notification 
{
    public function __construct(protected string $audience)
    {
    }

    public function title(): string
    {
        return __('dashboard.notifications.timetable_published_title');
    }

    public function body(): string
    {
        return $this->audience === 'teachers'
            ? __('dashboard.notifications.timetable_published_body_teacher')
            : __('dashboard.notifications.timetable_published_body_student');
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
            'type' => 'timetable_published',
            'audience' => $this->audience,
        ];
    }
}
