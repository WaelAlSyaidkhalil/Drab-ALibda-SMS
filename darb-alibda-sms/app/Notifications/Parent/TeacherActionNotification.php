<?php

namespace App\Notifications\Parent;

use App\Models\Academic\Student;
use App\Models\Auth\User;
use Illuminate\Notifications\Notification;

class TeacherActionNotification extends Notification
{
    public function __construct(
        protected User $teacher,
        protected Student $student,
        protected string $title,
        protected string $body,
        protected array $meta = []
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'parent',
            'title' => $this->title,
            'body' => $this->body,
            'student_id' => $this->student->id,
            'student_name' => $this->student->getFullNameAttribute(),
            'teacher_id' => $this->teacher->id,
            'teacher_name' => $this->teacher->name ?: $this->teacher->full_name,
            'type' => $this->meta['type'] ?? 'teacher_action',
            'meta' => $this->meta,
        ];
    }
}
