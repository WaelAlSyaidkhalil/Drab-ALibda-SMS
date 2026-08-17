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
        return 'تم نشر البرنامج';
    }

    public function body(): string
    {
        return $this->audience === 'teachers'
            ? 'تم نشر برنامج الأسبوع للمعلمين.'
            : 'تم نشر برنامج الأسبوع للطلاب.';
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
