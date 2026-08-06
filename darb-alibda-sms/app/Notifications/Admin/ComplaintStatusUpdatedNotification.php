<?php

namespace App\Notifications\Admin;

use App\Models\Communication\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ComplaintStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Complaint $complaint)
    {
    }

    public function title(): string
    {
        return __('dashboard.notifications.complaint_status_updated_title');
    }

    public function body(): string
    {
        return __('dashboard.notifications.complaint_status_updated_body', [
            'title' => $this->complaint->title,
            'status' => $this->complaint->status->label(),
        ]);
    }

    public function via($notifiable): array
    {
        return ['database'];
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
