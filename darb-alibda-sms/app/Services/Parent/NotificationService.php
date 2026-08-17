<?php

namespace App\Services\Parent;

use App\Repositories\Parent\NotificationRepository;

class NotificationService
{
    public function __construct(protected NotificationRepository $notificationRepository) {}

    public function getNotifications(int $userId): array
    {
        return $this->notificationRepository->getParentNotifications($userId);
    }

    public function markAsRead(int $userId, string $notificationId): array
    {
        return $this->notificationRepository->markAsRead($userId, $notificationId);
    }

    public function markAllAsRead(int $userId): int
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }

    public function deleteNotification(int $userId, string $notificationId): bool
    {
        return $this->notificationRepository->deleteParentNotification($userId, $notificationId);
    }
}
