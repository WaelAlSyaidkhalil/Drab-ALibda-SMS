<?php

namespace App\Services;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;

class NotificationService
{
    public function getAdminUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::query()
            ->whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })
            ->get();
    }

    /**
     * Send a notification to a single user.
     */
    public function send(User $user, Notification $notification): void
    {
        $user->notifyNow($notification);
    }

    /**
     * Send a notification to multiple users.
     *
     * @param iterable<int, User> $users
     */
    public function sendMany(iterable $users, Notification $notification): void
    {
        foreach ($users as $user) {
            if ($user instanceof User) {
                $this->send($user, $notification);
            }
        }
    }

    /**
     * Send a notification to multiple user IDs.
     *
     * @param iterable<int> $userIds
     */
    public function sendToUserIds(iterable $userIds, Notification $notification): void
    {
        $ids = collect($userIds)->filter(fn($id) => is_numeric($id))->values()->all();

        if ($ids === []) {
            return;
        }

        $users = User::query()->whereIn('id', $ids)->get();

        $this->sendMany($users, $notification);
    }
}
