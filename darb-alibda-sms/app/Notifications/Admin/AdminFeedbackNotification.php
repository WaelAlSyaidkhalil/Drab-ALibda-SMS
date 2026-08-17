<?php

namespace App\Notifications\Admin;

use Illuminate\Notifications\Notification;

class AdminFeedbackNotification extends Notification
{
    public function __construct(
        protected string $type,
        protected string $title,
        protected string $body,
        protected ?int $relatedId = null,
        protected ?string $relatedTitle = null,
        protected ?int $userId = null,
        protected ?string $userName = null,
        protected ?int $excuseRequestId = null,
        protected ?int $parentId = null,
        protected ?string $parentName = null,
        protected ?int $studentId = null,
        protected ?string $studentName = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'from' => 'parent',
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'related_id' => $this->relatedId,
            'related_title' => $this->relatedTitle,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'excuse_request_id' => $this->excuseRequestId,
            'parent_id' => $this->parentId,
            'parent_name' => $this->parentName,
            'student_id' => $this->studentId,
            'student_name' => $this->studentName,
        ];
    }

    public function getFirebaseTitle(): string
    {
        return $this->title;
    }

    public function getFirebaseBody(): string
    {
        return $this->body;
    }
}
