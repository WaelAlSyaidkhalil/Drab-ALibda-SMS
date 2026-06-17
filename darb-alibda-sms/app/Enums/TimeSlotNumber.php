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
            self::FIRST => 'الحصة الأولى',
            self::SECOND => 'الحصة الثانية',
            self::THIRD => 'الحصة الثالثة',
            self::FOURTH => 'الحصة الرابعة',
            self::FIFTH => 'الحصة الخامسة',
            self::SIXTH => 'الحصة السادسة',
            self::SEVENTH => 'الحصة السابعة',
        };
    }
    
    public static function getvalues(): array
    {
        return array_column(self::cases(), 'value');
    }
}