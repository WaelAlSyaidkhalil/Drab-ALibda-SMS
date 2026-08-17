<?php

namespace App\Notifications\Admin;

use App\Models\Auth\User;
use App\Models\Communication\AbsenceJustification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AbsenceJustificationStatusUpdatedNotification extends Notification
{
    public function __construct(
        protected AbsenceJustification $absenceJustification,
        protected ?User $admin = null,
        protected ?string $customTitle = null,
        protected ?string $customBody = null
    ) {
    }

    public function title(): string
    {
        if ($this->customTitle !== null) {
            return $this->customTitle;
        }

        return 'تم تحديث حالة تبرير الغياب';
    }

    public function body(): string
    {
        if ($this->customBody !== null) {
            return $this->customBody;
        }

        $studentName = $this->absenceJustification->student?->full_name
            ?? $this->absenceJustification->student?->user?->name
            ?? 'الطالب';

        $statusLabel = match ($this->absenceJustification->status) {
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'pending' => 'قيد الانتظار',
            default => $this->absenceJustification->status,
        };

        $absenceDate = $this->absenceJustification->absence_date
            ? $this->absenceJustification->absence_date->format('Y-m-d')
            : '';

        if (! empty($absenceDate)) {
            return sprintf('تم تحديث حالة طلب تبرير غياب %s بتاريخ %s إلى %s.', $studentName, $absenceDate, $statusLabel);
        }

        return sprintf('تم تحديث حالة طلب تبرير غياب %s إلى %s.', $studentName, $statusLabel);
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'admin',
            'admin_id' => $this->admin?->id,
            'admin_name' => $this->admin?->name,
            'title' => $this->title(),
            'body' => $this->body(),
            'absence_justification_id' => $this->absenceJustification->id,
            'student_id' => $this->absenceJustification->student_id,
            'student_name' => $this->absenceJustification->student?->full_name ?? $this->absenceJustification->student?->user?->name,
            'absence_date' => $this->absenceJustification->absence_date?->format('Y-m-d'),
            'status' => $this->absenceJustification->status,
            'review_note' => $this->absenceJustification->review_note,
            'type' => 'absence_justification_status_updated',
        ];
    }
}
