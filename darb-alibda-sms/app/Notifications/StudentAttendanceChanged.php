<?php

namespace App\Notifications;

use App\Models\Schedule\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class StudentAttendanceChanged extends Notification
{
    use Queueable;

    public Attendance $attendance;
    public string $event; // 'created'|'updated'

    public function __construct(Attendance $attendance, string $event = 'created')
    {
        $this->attendance = $attendance;
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['database', 'fcm'];
    }

    public function toDatabase($notifiable)
    {
        $student = $this->attendance->student;
        $status = is_object($this->attendance->status) ? $this->attendance->status->value : $this->attendance->status;
        $statusLabel = is_object($this->attendance->status) && method_exists($this->attendance->status, 'label') ? $this->attendance->status->label() : $status;
        $message = sprintf('%s: حالة الحضور لـ %s تم تحديدها كـ %s.', ucfirst($this->event), $student?->full_name ?? 'طالب', $statusLabel);

        return [
            'type' => 'attendance_changed',
            'attendance_id' => $this->attendance->id,
            'student_id' => $this->attendance->student_id,
            'status' => $status,
            'date' => $this->attendance->date?->toDateString(),
            'message' => $message,
        ];
    }
}
