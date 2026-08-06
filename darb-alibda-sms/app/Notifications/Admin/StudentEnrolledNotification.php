<?php

namespace App\Notifications\Admin;

use App\Models\Academic\StudentEnrollment;
use Illuminate\Notifications\Notification;

class StudentEnrolledNotification extends Notification
{

    public function __construct(protected StudentEnrollment $enrollment)
    {
    }

    public function title(): string
    {
        return __('dashboard.notifications.student_enrolled_title');
    }

    public function body(): string
    {
        return __('dashboard.notifications.student_enrolled_body', [
            'student' => $this->enrollment->student?->full_name ?? 'الطالب',
            'section' => $this->enrollment->section?->full_name ?? '',
            'year' => $this->enrollment->academic_year,
        ]);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => __('dashboard.notifications.student_enrolled_title'),
            'body' => __('dashboard.notifications.student_enrolled_body', [
                'student' => $this->enrollment->student?->full_name ?? 'الطالب',
                'section' => $this->enrollment->section?->full_name ?? '',
                'year' => $this->enrollment->academic_year,
            ]),
        ];
    }
}
