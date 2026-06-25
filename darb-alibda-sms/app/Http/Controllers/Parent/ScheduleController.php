<?php

namespace App\Http\Controllers\Parent;

use App\Models\Academic\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends ParentController
{
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $schedules = $student->getCurrentSection()?->schedules()->with(['subject', 'teacher.user', 'timeSlot', 'term'])->get();

        return $this->successResponse($schedules?->map(fn ($schedule) => [
            'day' => $schedule->day,
            'subject' => $schedule->subject->name,
            'teacher' => $schedule->teacher->user->name,
            'start_time' => $schedule->timeSlot->start_time,
            'end_time' => $schedule->timeSlot->end_time,
        ])->values() ?? [], 'تم جلب الجدول الدراسي بنجاح.');
    }
}
