<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Parent\ParentController;
use App\Services\Parent\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends ParentController
{
    public function __construct(protected NotificationService $notificationService) {}

    public function showNotification(Request $request)
    {
        try {
            $notifications = $this->notificationService->getNotifications($request->user()->id);

            return $this->successResponse($notifications, 'تم جلب الأشعارات بنجاح.');
        } catch (\Exception $e) {
            return $this->errorResponse(null, $e->getMessage(), 422);
        }
    }

    public function markAsRead(string $notificationId, Request $request)
    {
        try {
            $result = $this->notificationService->markAsRead($request->user()->id, $notificationId);

            return $this->successResponse($result, 'تم تعليم الإشعار كمقروء بنجاح.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->notFoundResponse('الإشعار غير موجود');
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }

    public function markAllAsRead(Request $request)
    {
        try {
            $count = $this->notificationService->markAllAsRead($request->user()->id);

            return $this->successResponse(
                ['marked_count' => $count],
                $count > 0 ? 'تم تعليم جميع الإشعارات غير المقروءة كمقروءة.' : 'لا توجد إشعارات غير مقروءة.'
            );
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }

    public function deleteNotification(string $notificationId, Request $request)
    {
        try {
            $deleted = $this->notificationService->deleteNotification($request->user()->id, $notificationId);

            if (! $deleted) {
                return $this->notFoundResponse('الإشعار غير موجود');
            }

            return $this->successResponse(null, 'تم حذف الإشعار بنجاح.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return $this->notFoundResponse('الإشعار غير موجود');
        } catch (\Exception $exception) {
            return $this->errorResponse(null, $exception->getMessage(), 422);
        }
    }
}
