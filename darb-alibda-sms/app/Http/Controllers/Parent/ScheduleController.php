<?php

namespace App\Http\Controllers\Parent;

use App\Enums\DayOfWeek;
use App\Models\Academic\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends ParentController
{
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $dayFilter = $request->query('day');
        $section = $student->getCurrentSection();

        $schedules = $section?->schedules()
            ->with(['subject', 'teacher', 'timeSlot', 'term'])
            ->when($dayFilter, fn($query) => $query->where('day', $dayFilter))
            ->orderBy('day')
            ->get();

        $grouped = ($schedules ?? collect())->groupBy('day')->map(function ($lessons, $day) {
            return [
                'day' => $day,
                'day_label' => $this->dayLabel($day),
                'lessons' => $lessons->map(function ($schedule) {
                    $timeSlot = $schedule->timeSlot;
                    return [
                        'subject' => $schedule->subject?->name,
                        'teacher' => $schedule->subject?->teacher
                            ? $schedule->subject->teacher->first_name . ' ' . $schedule->subject->teacher->last_name
                            : null,
                        'classroom' => $schedule->section?->name,
                        'start_time' => $timeSlot?->start_time?->format('H:i'),
                        'end_time' => $timeSlot?->end_time?->format('H:i'),
                        'duration_minutes' => $timeSlot ? $timeSlot->getDurationInMinutes() : null,
                        'term' => $schedule->term?->type,
                    ];
                })->values(),
            ];
        })->values();

        return $this->successResponse($grouped, 'تم جلب الجدول الدراسي بنجاح.', 200, [
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
            ],
            'classroom' => $section?->schoolClass?->name,
            'section' => $section?->name,
            'academic_year' => $student->getCurrentEnrollment()?->academic_year,
        ]);
    }

    private function dayLabel(string $day): string
    {
        return match ($day) {
            'Sun' => 'الأحد',
            'Mon' => 'الاثنين',
            'Tue' => 'الثلاثاء',
            'Wed' => 'الأربعاء',
            'Thu' => 'الخميس',
            default => $day,
        };
    }
}
