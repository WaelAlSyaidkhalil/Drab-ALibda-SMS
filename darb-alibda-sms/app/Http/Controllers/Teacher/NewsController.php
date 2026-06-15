<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\Teacher\TeacherNewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(protected TeacherNewsService $service)
    {
    }

    /**
     * عرض كل الأخبار
     */
    public function index(Request $request)
    {
        try {
            $news = $this->service->getAllNews($request->user()->id);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب الأخبار بنجاح',
                'data' => $news,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * عدد الأخبار غير المقروءة
     */
    public function unreadCount(Request $request)
    {
        try {
            $count = $this->service->getUnreadCount($request->user()->id);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب عدد الأخبار غير المقروءة',
                'data' => [
                    'unread_count' => $count,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تعليم خبر كمقروء
     */
    public function markAsRead(int $newsId, Request $request)
    {
        try {
            $this->service->markAsRead($request->user()->id, $newsId);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تعليم الخبر كمقروء بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تعليم كل الأخبار كمقروءة
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $result = $this->service->markAllAsRead($request->user()->id);

            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'data' => [
                    'marked_count' => $result['count'],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
