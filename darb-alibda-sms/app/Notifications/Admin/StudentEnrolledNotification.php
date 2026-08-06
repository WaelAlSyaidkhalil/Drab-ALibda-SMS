<?php

namespace App\Notifications\Admin;

use App\Models\Academic\StudentEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StudentEnrolledNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        $student = $this->enrollment->student;
        $section = $this->enrollment->section;

        return [
            'from' => 'school',
            'title' => __('dashboard.notifications.student_enrolled_title'),
            'body' => __('dashboard.notifications.student_enrolled_body', [
                'student' => $student?->full_name ?? 'الطالب',
                'section' => $section?->full_name ?? '',
                'year' => $this->enrollment->academic_year,
            ]),
            'type' => 'student_enrolled',
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $student?->id,
            'student_name' => $student?->full_name,
            'section_id' => $section?->id,
            'section_name' => $section?->full_name,
            'academic_year' => $this->enrollment->academic_year,
        ];
    }
}
