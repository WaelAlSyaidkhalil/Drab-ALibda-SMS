<?php

namespace App\Notifications\Admin;

use App\Models\Schedule\TeacherAttendance;
use Illuminate\Notifications\Notification;

class TeacherAbsentNotification extends Notification 
{

    public function __construct(protected TeacherAttendance $attendance)
    {
    }

    public function title(): string
    {
        return __('dashboard.notifications.teacher_absent_title');
    }

    public function body(): string
    {
        return __('dashboard.notifications.teacher_absent_body', [
            'teacher' => $this->attendance->teacher->full_name,
            'date' => $this->attendance->date?->format('Y-m-d') ?: '',
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
            'teacher_id' => $this->attendance->teacher_id,
            'teacher_name' => $this->attendance->teacher->full_name,
            'status' => $this->attendance->status,
            'date' => $this->attendance->date?->toDateString(),
            'type' => 'teacher_absent',
        ];
    }
}
