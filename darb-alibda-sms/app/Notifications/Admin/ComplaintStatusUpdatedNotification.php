<?php

namespace App\Notifications\Admin;

use App\Models\Communication\Complaint;
use Illuminate\Notifications\Notification;

class ComplaintStatusUpdatedNotification extends Notification
{

    public function __construct(protected Complaint $complaint)
    {
    }

    public function title(): string
    {
        return 'تم تحديث حالة الشكوى';
    }

    public function body(): string
    {
        return 'حالة شكواك "' . $this->complaint->title . '" أصبحت ' . $this->complaint->status->label() . '.';
    }

    public function via($notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'admin',
            'title' => $this->title(),
            'body' => $this->body(),
            'complaint_id' => $this->complaint->id,
            'complaint_title' => $this->complaint->title,
            'complaint_status' => $this->complaint->status->value,
            'complaint_status_label' => $this->complaint->status->label(),
            'type' => 'complaint_status_updated',
        ];
    }
}
