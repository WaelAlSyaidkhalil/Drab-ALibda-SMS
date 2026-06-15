<?php

namespace App\Repositories\Teacher;

use App\Models\Academic\Section;
use App\Models\Academic\StudentEnrollment;
use App\Models\Schedule\Attendance;
use App\Models\Schedule\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeacherAttendanceRepository
{
    /**
     * الحصول على الشعب التي يدرسها المعلم مع الطلاب
     */
    public function getTeacherSectionsWithStudents(int $teacherId, ?int $classId, ?int $sectionId, ?string $date): Collection
    {
        $sectionIds = Schedule::query()
            ->forTeacher($teacherId)
            ->when($classId, fn ($query, $classId) =>
                $query->whereHas('section', fn ($query) => $query->where('class_id', $classId))
            )
            ->when($sectionId, fn ($query, $sectionId) =>
                $query->where('section_id', $sectionId)
            )
            ->pluck('section_id')
            ->unique()
            ->values();

        if ($sectionIds->isEmpty()) {
            return collect();
        }

        $sections = Section::with([
            'schoolClass',
            'enrollments' => fn ($query) => $query->active()->with(['student.user', 'student.parent']),
        ])->whereIn('id', $sectionIds)
            ->orderBy('class_id')
            ->orderBy('name')
            ->get();

        $date = Carbon::parse($date ?? now()->toDateString())->toDateString();

        return $sections->map(function (Section $section) use ($teacherId, $date) {
            $scheduleIds = Schedule::forTeacher($teacherId)
                ->where('section_id', $section->id)
                ->pluck('id');

            $attendance = Attendance::query()
                ->whereIn('schedule_id', $scheduleIds)
                ->whereDate('date', $date)
                ->get();

            $studentStatuses = $attendance->groupBy('student_id')->map(function ($records) {
                if ($records->contains('status', 'absent')) {
                    return 'absent';
                }

                if ($records->contains('status', 'late')) {
                    return 'late';
                }

                if ($records->contains('status', 'excused')) {
                    return 'excused';
                }

                return 'present';
            });

            $presentCount = $studentStatuses->where('present')->count();
            $absentCount = $studentStatuses->where('absent')->count();
            $lateCount = $studentStatuses->where('late')->count();
            $excusedCount = $studentStatuses->where('excused')->count();
            $totalStudents = $section->enrollments->count();
            $percentage = $totalStudents > 0 ? round(($presentCount / $totalStudents) * 100, 2) : 0;

            $students = $section->enrollments->map(function ($enrollment) use ($studentStatuses) {
                return [
                    'student_id' => $enrollment->student->id,
                    'enrollment_id' => $enrollment->id,
                    'registry_number' => $enrollment->student->registry_number,
                    'full_name' => $enrollment->student->full_name,
                    'first_name' => $enrollment->student->first_name,
                    'last_name' => $enrollment->student->last_name,
                    'email' => $enrollment->student->user->email,
                    'phone' => $enrollment->student->user->phone,
                    'gender' => $enrollment->student->gender,
                    'birth_date' => $enrollment->student->birth_date?->format('Y-m-d'),
                    'parent' => $enrollment->student->parent ? [
                        'id' => $enrollment->student->parent->id,
                        'name' => $enrollment->student->parent->name,
                        'email' => $enrollment->student->parent->email,
                        'phone' => $enrollment->student->parent->phone,
                    ] : null,
                    'attendance_status' => $studentStatuses->get($enrollment->student->id, 'present'),
                ];
            });

            return [
                'section_id' => $section->id,
                'section_name' => $section->name,
                'section_full_name' => $section->full_name,
                'class_id' => $section->class_id,
                'class_name' => $section->schoolClass->name,
                'total_students' => $totalStudents,
                'attendance' => [
                    'date' => $date,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'late' => $lateCount,
                    'excused' => $excusedCount,
                    'percentage' => $percentage,
                ],
                'schedules' => Schedule::forTeacher($teacherId)
                    ->where('section_id', $section->id)
                    ->with(['subject', 'timeSlot', 'term'])
                    ->orderBy('day', 'asc')
                    ->orderBy('time_slot_id', 'asc')
                    ->get()
                    ->map(fn ($schedule) => [
                        'schedule_id' => $schedule->id,
                        'subject_name' => $schedule->subject->name,
                        'day' => $schedule->day,
                        'time_slot' => [
                            'id' => $schedule->timeSlot->id,
                            'name' => $schedule->timeSlot->name,
                            'start_time' => $schedule->timeSlot->start_time,
                            'end_time' => $schedule->timeSlot->end_time,
                        ],
                        'term_name' => $schedule->term->name,
                    ]),
                'students' => $students,
            ];
        });
    }

    /**
     * احصل على شعبة المعلم وتجهيز IDs للحضور
     */
    public function getTeacherSectionScheduleIds(int $teacherId, int $sectionId, ?int $scheduleId = null)
    {
        $query = Schedule::forTeacher($teacherId)
            ->where('section_id', $sectionId);

        if ($scheduleId) {
            $query->where('id', $scheduleId);
        }

        return $query->pluck('id');
    }

    /**
     * الحصول على طلبات الحاضرون الفعّالة في الشعبة
     */
    public function getActiveStudentIdsForSection(int $sectionId): Collection
    {
        return StudentEnrollment::active()
            ->where('section_id', $sectionId)
            ->pluck('student_id');
    }

    /**
     * مزامنة الحضور للشعبة بتاريخ محدد
     */
    public function syncSectionAttendance(int $sectionId, string $date, Collection $studentStatuses, Collection $scheduleIds): array
    {
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;

        foreach ($studentStatuses as $studentId => $statusData) {
            $status = $statusData['status'];

            if ($status === 'present') {
                $presentCount++;
            } elseif ($status === 'absent') {
                $absentCount++;
            } elseif ($status === 'late') {
                $lateCount++;
            }

            foreach ($scheduleIds as $scheduleId) {
                Attendance::updateOrCreate(
                    [
                        'schedule_id' => $scheduleId,
                        'student_id' => $studentId,
                        'date' => $date,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }
        }

        return [
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'attendance_rate' => $presentCount *100 /$studentStatuses ->count()
        ];
    }
}
