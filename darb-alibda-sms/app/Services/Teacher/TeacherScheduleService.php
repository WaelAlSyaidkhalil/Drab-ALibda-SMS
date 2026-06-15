<?php

namespace App\Services\Teacher;

use App\Repositories\Teacher\TeacherScheduleRepository;

class TeacherScheduleService
{
    public function __construct(protected TeacherScheduleRepository $repository)
    {
    }

    /**
     * احصل على برنامج اليوم
     */
    public function getTodaySchedule(int $teacherId)
    {
        $schedules = $this->repository->getTodaySchedule($teacherId);

        return $schedules->map(fn ($schedule) => [
            'id' => $schedule->id,
            'subject' => $schedule->subject->name,
            'section' => $schedule->section->full_name,
            'class' => $schedule->section->schoolClass->name,
            'day' => $schedule->day,
            'time_slot' => [
                'id' => $schedule->timeSlot->id,
                'period_number' => $schedule->timeSlot->period_number,
                'name' => $schedule->timeSlot->name,
                'start_time' => $schedule->timeSlot->start_time,
                'end_time' => $schedule->timeSlot->end_time,
            ],
            'term' => $schedule->term->name,
        ]);
    }

    /**
     * احصل على برنامج الأسبوع
     */
    public function getWeekSchedule(int $teacherId)
    {
        return $this->repository->getWeekSchedule($teacherId);
    }
}
