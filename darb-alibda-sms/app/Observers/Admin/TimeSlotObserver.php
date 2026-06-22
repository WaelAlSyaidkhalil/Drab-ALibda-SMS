<?php

namespace App\Observers\Admin;

use App\Models\Schedule\TimeSlot;
use App\Services\Admin\TimeSlotService;

class TimeSlotObserver
{
    public function created(TimeSlot $timeSlot): void
    {
        TimeSlotService::reorder();
    }

    public function creating(TimeSlot $timeSlot): void
    {
        $conflicts = TimeSlotService::hasConflict($timeSlot->start_time, $timeSlot->end_time);

        if ($conflicts) 
            throw new \Exception('توقيت الحصة يتعارض مع حصة أخرى. يرجى تعديل الأوقات لتجنب التعارض.');

    }

    public function deleted(TimeSlot $timeSlot): void
    {
        TimeSlotService::reorder();
    }

    public function updated(TimeSlot $timeSlot): void
    {
        if ($timeSlot->wasChanged(['start_time'])) {
            TimeSlotService::reorder();
        }
    }

    public function updating(TimeSlot $timeSlot): void
    {
        $conflicts = TimeSlotService::hasConflict($timeSlot->start_time, $timeSlot->end_time, $timeSlot->id);

        if ($conflicts) 
            throw new \Exception('توقيت الحصة يتعارض مع حصة أخرى. يرجى تعديل الأوقات لتجنب التعارض.');

    }


}