<?php

namespace App\Notifications\Admin;

use App\Enums\MarkResult;
use App\Models\Academic\StudentEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class StudentEnrollmentStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected StudentEnrollment $enrollment)
    {
    }

    public function title(): string
    {
        return __('dashboard.notifications.student_enrollment_status_updated_title');
    }

    public function body(): string
    {
        $student = $this->enrollment->student?->full_name ?? __('dashboard.notifications.student');
        $section = $this->enrollment->section?->full_name ?? '';
        $status = $this->enrollment->status->label();
        $finalResult = $this->enrollment->final_result;

        if ($finalResult !== null && $finalResult !== MarkResult::PENDING) {
            return __('dashboard.notifications.student_enrollment_status_updated_body_with_result', [
                'student' => $student,
                'section' => $section,
                'status' => $status,
                'result' => $finalResult->label(),
            ]);
        }

        return __('dashboard.notifications.student_enrollment_status_updated_body', [
            'student' => $student,
            'section' => $section,
            'status' => $status,
        ]);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'school',
            'title' => $this->title(),
            'body' => $this->body(),
            'type' => 'student_enrollment_status_updated',
            'enrollment_id' => $this->enrollment->id,
            'student_id' => $this->enrollment->student?->id,
            'student_name' => $this->enrollment->student?->full_name,
            'section_id' => $this->enrollment->section?->id,
            'section_name' => $this->enrollment->section?->full_name,
            'academic_year' => $this->enrollment->academic_year,
            'status' => $this->enrollment->status->value,
            'status_label' => $this->enrollment->status->label(),
            'final_result' => $this->enrollment->final_result?->value,
            'final_result_label' => $this->enrollment->final_result?->label(),
        ];
    }
}
