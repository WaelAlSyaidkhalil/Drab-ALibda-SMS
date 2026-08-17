<?php

namespace App\Notifications\Admin;

use App\Models\Academic\Teacher;
use Illuminate\Notifications\Notification;

class TeacherCreatedNotification extends Notification
{

    public function __construct(protected Teacher $teacher)
    {
    }

    public function title(): string
    {
        return 'تم إنشاء حساب المعلم';
    }

    public function body(): string
    {
        return 'تم إنشاء حساب المعلم ' . $this->teacher->full_name . ' ويمكنه الآن الوصول إلى النظام.';
    }

    public function via($notifiable): array
    {
        return ['database', 'fcm'];
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
