<?php

namespace App\Http\Controllers\Parent;

use App\Models\Academic\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends ParentController
{
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        return $this->successResponse([
            'present_days' => 0,
            'absent_days' => 0,
            'excused_absences' => 0,
            'unexcused_absences' => 0,
            'attendance_percentage' => 0,
        ], 'تم جلب الحضور بنجاح.');
    }
}
