<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\Teacher\TeacherScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(protected TeacherScheduleService $service)
    {
    }

    /**
     * برنامج اليوم الحالي
     */
    public function today(Request $request)
    {
        try {
            $teacher = $request->user()->teacher;

            if (!$teacher) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المستخدم ليس معلماً',
                ], 403);
            }

            $schedules = $this->service->getTodaySchedule($teacher->id);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب برنامج اليوم بنجاح',
                'data' => $schedules,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * برنامج الأسبوع
     */
    public function week(Request $request)
    {
        try {
            $teacher = $request->user()->teacher;

            if (!$teacher) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المستخدم ليس معلماً',
                ], 403);
            }

            $schedules = $this->service->getWeekSchedule($teacher->id);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب برنامج الأسبوع بنجاح',
                'data' => $schedules,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
