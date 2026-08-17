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
        return 'تم تسجيل الطالب';
    }

    public function body(): string
    {
        $student = $this->enrollment->student?->full_name ?? 'الطالب';
        $section = $this->enrollment->section?->full_name ?? 'الفصل';
        $year = $this->enrollment->academic_year;

        return $student . ' تم تسجيله في ' . $section . ' للسنة الدراسية ' . $year . '.';
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $student = $this->enrollment->student?->full_name ?? 'الطالب';
        $section = $this->enrollment->section?->full_name ?? 'الفصل';
        $year = $this->enrollment->academic_year;

        return [
            'title' => 'تم تسجيل الطالب',
            'body' => $student . ' تم تسجيله في ' . $section . ' للسنة الدراسية ' . $year . '.',
        ];
    }
}
