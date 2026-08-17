<?php

namespace App\Notifications\Parent;

use App\Models\Academic\Student;
use App\Models\Auth\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeacherActionNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected User $teacher,
        protected Student $student,
        protected string $title,
        protected string $body,
        protected array $meta = []
    ) {}

    /**
     * القنوات التي سيتم الإرسال إليها
     */
    public function via($notifiable): array
    {
        return ['database', 'fcm'];
    }

    /**
     * حفظ الإشعار في Database
     */
    public function toDatabase($notifiable): array
    {
        return $this->notificationData();
    }

    public function getFirebaseTitle(): string
    {
        return $this->title;
    }

    public function getFirebaseBody(): string
    {
        return $this->body;
    }

    /**
     * البيانات المشتركة بين Database و FCM
     */
    protected function notificationData(): array
    {
        return [
            'from' => 'teacher',

            'title' => $this->title,

            'body' => $this->body,

            'student_id' => $this->student->id,

            'student_name' => $this->student->full_name,

            'teacher_id' => $this->teacher->id,

            'teacher_name' => $this->teacher->full_name,

            'type' => $this->meta['type'] ?? 'teacher_action',

            'meta' => $this->meta,
        ];
    }
}
