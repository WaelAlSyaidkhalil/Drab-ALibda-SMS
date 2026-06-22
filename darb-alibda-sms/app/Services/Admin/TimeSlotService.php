<?php

namespace App\Services\Admin;

use App\Models\Schedule\TimeSlot;

class TimeSlotService
{
    public static function reorder(): void
    {
        $slots = TimeSlot::query()
            ->orderBy('start_time')
            ->get();

        foreach ($slots as $index => $slot) {
            $slot->updateQuietly([
                'period_number' => $index + 1,
            ]);
        }
    }


public static function hasConflict(string $startTime, string $endTime, ?int $ignoreId = null,): bool 
{
    return TimeSlot::query()
        ->when(
            $ignoreId,
            fn ($query) => $query->whereKeyNot($ignoreId)
        )
        ->where('start_time', '<', $endTime)
        ->where('end_time', '>', $startTime)
        ->exists();
}
}