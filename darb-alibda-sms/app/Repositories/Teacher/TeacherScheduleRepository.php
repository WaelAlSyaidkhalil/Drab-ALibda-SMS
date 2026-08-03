<?php

namespace App\Repositories\Teacher;

use App\Models\Schedule\Schedule;
use Carbon\Carbon;

class TeacherScheduleRepository
{
    /**
     * احصل على برنامج اليوم الحالي
     */
    public function getTodaySchedule(int $teacherId)
    {
        $today = Carbon::today();
        $dayOfWeek = strtolower(Carbon::today()->format('D'));

        $dayMap = [
            'sun' => 'sun',
            'mon' => 'mon',
            'tue' => 'tue',
            'wed' => 'wed',
            'thu' => 'thu',
            'fri' => 'fri',
            'sat' => 'sat',
        ];

        $mappedDay = $dayMap[$dayOfWeek] ?? null;

        if (!$mappedDay) {
            return collect();
        }

        return Schedule::whereHas('subject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->where('day', $mappedDay)
            ->with([
                'section' => fn ($q) => $q->with('schoolClass'),
                'subject',
                'timeSlot',
                'term',
            ])
            ->orderBy('time_slot_id', 'asc')
            ->get();
    }

    /**
     * احصل على برنامج الأسبوع (الأحد - الخميس)
     */
    public function getWeekSchedule(int $teacherId)
    {
        $weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu'];


        $schedules = Schedule::whereHas('subject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->whereIn('day', $weekDays)
            ->with([
                'section' => fn ($q) => $q->with('schoolClass'),
                'subject',
                'timeSlot',
                'term',
            ])
            ->orderBy('day', 'asc')
            ->orderBy('time_slot_id', 'asc')
            ->get();

        return $schedules->groupBy('day')->mapWithKeys(function ($items, $day) {
          $dayNames = [
    'Sun' => 'الأحد',
    'Mon' => 'الإثنين',
    'Tue' => 'الثلاثاء',
    'Wed' => 'الأربعاء',
    'Thu' => 'الخميس',
];


            return [
                $dayNames[$day] => $items->map(fn ($schedule) => [
                    'id' => $schedule->id,
                    'subject' => $schedule->subject->name,
                    'section' => $schedule->section->full_name,
                    'time_slot' => [
                        'id' => $schedule->timeSlot->id,
                        'name' => $schedule->timeSlot->name,
                        'start_time' => $schedule->timeSlot->start_time,
                        'end_time' => $schedule->timeSlot->end_time,
                    ],
                ]),
            ];
        });
    }
}
