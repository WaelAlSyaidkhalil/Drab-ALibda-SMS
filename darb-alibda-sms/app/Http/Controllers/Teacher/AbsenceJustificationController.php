<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\UpdateAbsenceJustificationRequest;
use App\Services\Teacher\TeacherAbsenceService;
use Illuminate\Http\Request;

class AbsenceJustificationController extends Controller
{
    public function __construct(protected TeacherAbsenceService $service)
    {
    }

    /**
     * عرض كل طلبات الغياب الخاصة بطلاب المعلم
     */
    public function index(Request $request)
    {
        try {
            $teacher = $request->user()->teacher;

            if (!$teacher) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المستخدم ليس معلماً',
                ], 403);
            }

            $justifications = $this->service->getAbsenceJustifications($teacher->id);

            return response()->json([
                'status' => 'success',
                'message' => 'تم جلب طلبات الغياب بنجاح',
                'data' => $justifications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تحديث طلب غياب
     */
    public function update(int $justificationId, UpdateAbsenceJustificationRequest $request)
    {
        try {
            $user = $request->user();

            $updated = $this->service->updateAbsenceJustification(
                $justificationId,
                $request->validated(),
                $user->id
            );

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث طلب الغياب بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * حذف طلب غياب
     */
    public function destroy(int $justificationId, Request $request)
    {
        try {
            $this->service->deleteAbsenceJustification($justificationId);

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف طلب الغياب بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
