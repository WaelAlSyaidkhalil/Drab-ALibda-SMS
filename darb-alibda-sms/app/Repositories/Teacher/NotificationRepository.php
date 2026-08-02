<?php

namespace App\Repositories\Teacher;

use App\Models\Auth\User;
use Illuminate\Support\Carbon;

class NotificationRepository
{
    public function getTeacherNotifications(int $userId): array
    {
        $user = User::findOrFail($userId);

        $notifications = $user->notifications()
            ->latest('created_at')
            ->get()
            ->map(function ($notification) {
                $data = is_array($notification->data) ? $notification->data : (array) ($notification->data ?? []);
                $source = strtolower((string) ($data['from'] ?? 'admin'));
                $group = $source === 'parent' ? 'parent' : 'admin';

                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'إشعار جديد',
                    'body' => $data['body'] ?? '',
                    'group' => $group,
                    'group_label' => 'ولي أمر',
                    'student_name' => $data['student_name'] ?? null,
                    'teacher_name' => $data['teacher_name'] ?? null,
                    'type' => $data['type'] ?? null,
                    'is_read' => ! empty($notification->read_at),
                    'read_at' => $notification->read_at?->toDateTimeString(),
                    'created_at' => $notification->created_at?->toDateTimeString(),
                ];
            });

        $grouped = [
            'unread' => [
                'parent' => [],
            ],
            'read' => [
                'parent' => [],
            ],
        ];

        foreach ($notifications as $item) {
            $bucket = $item['is_read'] ? 'read' : 'unread';
            $grouped[$bucket]['parent'][] = $item;
        }

        return [
            'unread' => $grouped['unread'],
            'read' => $grouped['read'],
            'unread_count' => count($grouped['unread']['parent']),
            'read_count' => count($grouped['read']['parent']),
        ];
    }

    public function markAsRead(int $userId, string $notificationId): array
    {
        $user = User::findOrFail($userId);
        $notification = $user->notifications()->where('id', $notificationId)->firstOrFail();

        if (empty($notification->read_at)) {
            $notification->markAsRead();
        }

        return [
            'id' => $notification->id,
            'is_read' => true,
        ];
    }

    public function markAllAsRead(int $userId): int
    {
        $user = User::findOrFail($userId);
        $notifications = $user->notifications()->whereNull('read_at')->get();

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return $notifications->count();
    }

    public function deleteTeacherNotification(int $userId, string $notificationId): bool
    {
        $user = User::findOrFail($userId);
        $notification = $user->notifications()->where('id', $notificationId)->firstOrFail();

        return (bool) $notification->delete();
    }
}