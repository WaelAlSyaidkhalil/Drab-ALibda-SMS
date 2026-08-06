<?php

namespace App\Notifications\Admin;

use App\Enums\StudentStatus;
use Illuminate\Notifications\Notification;

class StudentEnrollmentStatusUpdatedNotification extends Notification
{
    public function __construct(
        protected string $studentName,
        protected string $statusValue,
        protected string $statusLabel,
        protected ?float $finalAverage,
    ) {
    }

    public function title(): string
    {
        $status = StudentStatus::from($this->statusValue);

        if ($status == StudentStatus::GRADUATED || $status == StudentStatus::PROMOTED) {
            return __('dashboard.notifications.student_enrollment_status_updated_title_success');
        }

        if ($status == StudentStatus::REPEATED) {
            return __('dashboard.notifications.student_enrollment_status_updated_title_failed');
        }

        if ($status == StudentStatus::WITHDRAWN) {
            return __('dashboard.notifications.student_enrollment_status_updated_title_withdrawn');
        }

        return __('dashboard.notifications.student_enrollment_status_updated_title');
    }

    public function body(): string
    {
        $status = StudentStatus::from($this->statusValue);
        $average = $this->finalAverage !== null
            ? number_format($this->finalAverage, 2)
            : null;

        if ($status == StudentStatus::GRADUATED && $average !== null) {
            return __('dashboard.notifications.student_enrollment_status_updated_body_graduated', [
                'student' => $this->studentName,
                'average' => $average,
            ]);
        }

        if ($status == StudentStatus::PROMOTED && $average !== null) {
            return __('dashboard.notifications.student_enrollment_status_updated_body_promoted', [
                'student' => $this->studentName,
                'average' => $average,
            ]);
        }

        if ($status == StudentStatus::REPEATED && $average !== null) {
            return __('dashboard.notifications.student_enrollment_status_updated_body_repeated', [
                'student' => $this->studentName,
                'average' => $average,
            ]);
        }

        if ($status == StudentStatus::WITHDRAWN && $average !== null) {
            return __('dashboard.notifications.student_enrollment_status_updated_body_withdrawn', [
                'student' => $this->studentName,
                'average' => $average,
            ]);
        }

        return __('dashboard.notifications.student_enrollment_status_updated_body', [
            'student' => $this->studentName,
            'status' => $this->statusLabel,
        ]);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->title(),
            'body' => $this->body(),
        ];
    }
}
