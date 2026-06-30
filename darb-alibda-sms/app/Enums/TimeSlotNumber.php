<?php

namespace App\Enums;

enum TimeSlotNumber: int
{
    case FIRST = 1;
    case SECOND = 2;
    case THIRD = 3;
    case FOURTH = 4;
    case FIFTH = 5;
    case SIXTH = 6;
    case SEVENTH = 7;

    public function label(): string
    {
        return match ($this) {
            self::FIRST => __('dashboard.enums.time_slot.first'),
            self::SECOND => __('dashboard.enums.time_slot.second'),
            self::THIRD => __('dashboard.enums.time_slot.third'),
            self::FOURTH => __('dashboard.enums.time_slot.fourth'),
            self::FIFTH => __('dashboard.enums.time_slot.fifth'),
            self::SIXTH => __('dashboard.enums.time_slot.sixth'),
            self::SEVENTH => __('dashboard.enums.time_slot.seventh'),
        };
    }
    
    public static function getvalues(): array
    {
        return array_column(self::cases(), 'value');
    }
}