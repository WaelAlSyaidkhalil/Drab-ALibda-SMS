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
        return 'تم تحديث حالة الطالب';
    }

    public function body(): string
    {
        $status = StudentStatus::from($this->statusValue);
        $average = $this->finalAverage !== null
            ? number_format($this->finalAverage, 2)
            : null;

        $base = 'حالة تسجيل الطالب "' . $this->studentName . '" أصبحت ' . $this->statusLabel;

        if ($status == StudentStatus::GRADUATED && $average !== null) {
            return $base . ' بمعدل نهائي ' . $average . '. تهانينا!';
        }

        if ($status == StudentStatus::PROMOTED && $average !== null) {
            return $base . ' بمعدل نهائي ' . $average . '.';
        }

        if ($status == StudentStatus::REPEATED && $average !== null) {
            return $base . ' بمعدل نهائي ' . $average . '.';
        }

        if ($status == StudentStatus::WITHDRAWN && $average !== null) {
            return $base . ' بمعدل نهائي ' . $average . '.';
        }

        return $base . '.';
    }

    public function via($notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->title(),
            'body' => $this->body(),
        ];
    }
}
