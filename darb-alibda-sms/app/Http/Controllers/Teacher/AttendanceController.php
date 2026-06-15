<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\UpdateSectionAttendanceRequest;
use App\Services\Teacher\TeacherAttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(protected TeacherAttendanceService $service)
    {
    }

    public function sectionsWithStudents(Request $request)
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'المستخدم ليس معلماً',
            ], 403);
        }

        $sections = $this->service->getSectionsWithStudents(
            $teacher->id,
            $request->query('class_id'),
            $request->query('section_id'),
            $request->query('date')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب الصفوف والشعب والطلاب بنجاح',
            'data' => $sections,
        ]);
    }

    public function batchUpdateSectionAttendance(UpdateSectionAttendanceRequest $request, int $sectionId)
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json([
                'status' => 'error',
                'message' => 'المستخدم ليس معلماً',
            ], 403);
        }

        try {
            $result = $this->service->updateSectionAttendance(
                $teacher->id,
                $sectionId,
                $request->input('date', now()->toDateString()),
                $request->input('students', []),
                $request->input('schedule_id')
            );

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث حالات الحضور بنجاح',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
