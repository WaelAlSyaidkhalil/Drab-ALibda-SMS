<?php

namespace App\Notifications\Admin;

use App\Models\Academic\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TeacherCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Teacher $teacher)
    {
    }

    public function title(): string
    {
        return __('dashboard.notifications.teacher_created_title');
    }

    public function body(): string
    {
        return __('dashboard.notifications.teacher_created_body', [
            'teacher' => $this->teacher->full_name,
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
            'teacher_id' => $this->teacher->id,
            'teacher_name' => $this->teacher->full_name,
            'type' => 'teacher_created',
        ];
    }
}
