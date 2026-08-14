<?php

namespace App\Notifications\Admin;

use App\Models\Communication\Complaint;
use Illuminate\Notifications\Notification;

class ComplaintSubmittedNotification extends Notification
{
    public function __construct(protected Complaint $complaint)
    {
    }

    public function title(): string
    {
        return 'تم إرسال شكوى جديدة';
    }

    public function body(): string
    {
        return 'تم إرسال شكوى جديدة بعنوان: ' . $this->complaint->title . ' من المستخدم ' . ($this->complaint->user?->name ?? 'مستخدم');
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'user',
            'title' => $this->title(),
            'body' => $this->body(),
            'complaint_id' => $this->complaint->id,
            'complaint_title' => $this->complaint->title,
            'user_id' => $this->complaint->user_id,
            'user_name' => $this->complaint->user?->name,
            'type' => 'complaint_submitted',
        ];
    }
}
