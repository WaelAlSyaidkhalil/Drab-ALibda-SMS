<?php

namespace App\Services\Teacher;

use App\Repositories\Teacher\TeacherNewsRepository;

class TeacherNewsService
{
    public function __construct(protected TeacherNewsRepository $repository)
    {
    }

    /**
     * احصل على كل الأخبار مع ترتيب غير المقروء أولاً
     */
    public function getAllNews(int $userId)
    {
        $news = $this->repository->getAllNews($userId);

        return $news->sortBy(fn ($n) => [
            $n->is_read ? 1 : 0,
            -$n->created_at->timestamp,
        ])->values()->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'body' => $n->body,
            'audience' => $n->audience,
            'is_read' => $n->is_read,
            'creator' => [
                'id' => $n->user->id,
                'name' => $n->user->name,
                'email' => $n->user->email,
            ],
            'attachments' => $n->attachments->map(fn ($a) => [
                'id' => $a->id,
                'path' => $a->path,
                'file_name' => $a->file_name,
            ]),
            'created_at' => $n->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * احصل على عدد الأخبار غير المقروءة
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->repository->getUnreadCount($userId);
    }

    /**
     * علّم خبراً كمقروء
     */
    public function markAsRead(int $userId, int $newsId)
    {
        $marked = $this->repository->markAsRead($userId, $newsId);

        if (!$marked) {
            throw new \Exception('فشل تعليم الخبر كمقروء');
        }

        return true;
    }

    /**
     * علّم كل الأخبار كمقروءة
     */
    public function markAllAsRead(int $userId)
    {
        $count = $this->repository->markAllAsRead($userId);

        return [
            'message' => 'تم تعليم ' . $count . ' أخبار كمقروءة',
            'count' => $count,
        ];
    }
}
