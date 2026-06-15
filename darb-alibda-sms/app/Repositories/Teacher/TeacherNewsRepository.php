<?php

namespace App\Repositories\Teacher;

use App\Models\Communication\News;
use App\Models\Auth\User;
use Carbon\Carbon;

class TeacherNewsRepository
{
    /**
     * احصل على كل الأخبار المتاحة للمعلم مع حالة القراءة
     */
    public function getAllNews(int $userId)
    {
        return News::forTeachers()
            ->with([
                'user' => fn ($q) => $q->select('id', 'name', 'email'),
                'attachments',
                'readers' => fn ($q) => $q->where('user_id', $userId),
            ])
            ->latest()
            ->get()
            ->map(function ($news) use ($userId) {
                $news->is_read = $news->readers->isNotEmpty();
                return $news;
            });
    }

    /**
     * احصل على عدد الأخبار غير المقروءة
     */
    public function getUnreadCount(int $userId): int
    {
        return News::forTeachers()
            ->whereDoesntHave('readers', fn ($query) =>
                $query->where('user_id', $userId)
            )
            ->count();
    }

    /**
     * علّم خبراً معيناً كمقروء
     */
    public function markAsRead(int $userId, int $newsId)
    {
        $news = News::find($newsId);

        if (!$news) {
            return false;
        }

        $news->readers()->syncWithoutDetaching([$userId]);

        return true;
    }

    /**
     * علّم كل الأخبار كمقروءة
     */
    public function markAllAsRead(int $userId)
    {
        $unreadNews = News::forTeachers()
            ->whereDoesntHave('readers', fn ($query) =>
                $query->where('user_id', $userId)
            )
            ->pluck('id');

        if ($unreadNews->isEmpty()) {
            return 0;
        }

        $syncData = [];
        foreach ($unreadNews as $newsId) {
            $syncData[$newsId] = [];
        }

        $user = User::find($userId);

        foreach ($unreadNews as $newsId) {
            $user->readNews()->attach($newsId);
        }

        return $unreadNews->count();
    }
}
