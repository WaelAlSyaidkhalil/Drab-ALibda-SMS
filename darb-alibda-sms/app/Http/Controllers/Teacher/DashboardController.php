<?php

namespace App\Http\Controllers\Teacher;

use App\Services\Teacher\TeacherDashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends TeacherController
{
    public function overview(Request $request, TeacherDashboardService $dashboardService): JsonResponse
    {
        $teacher = $request->user()?->teacher;

        if (! $teacher) {
            return $this->errorResponse(null, 'حساب المعلم غير مرتبط ببيانات المعلم.', 404);
        }

        $data = $dashboardService->getOverview($teacher->id, $request->user()->id);

        return $this->successResponse($data, 'تم جلب ملخص لوحة المعلم بنجاح.');
    }
}
