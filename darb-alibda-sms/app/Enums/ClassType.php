<?php

namespace App\Enums;

enum ClassType: string
{
    case PRIMARY_FIRST = 'primary_first';
    case PRIMARY_SECOND = 'primary_second';
    case PRIMARY_THIRD = 'primary_third';
    case PRIMARY_FOURTH = 'primary_fourth';
    case PRIMARY_FIFTH = 'primary_fifth';
    case PRIMARY_SIXTH = 'primary_sixth';
    case MIDDLE_FIRST = 'middle_first';
    case MIDDLE_SECOND = 'middle_second';
    case MIDDLE_THIRD = 'middle_third';
    case SECONDARY_FIRST = 'secondary_first';
    case SECONDARY_SECOND = 'secondary_second';
    case SECONDARY_THIRD = 'secondary_third';

    public function label(): string
    {
        return match ($this) {
            self::PRIMARY_FIRST => 'الصف الأول الابتدائي',
            self::PRIMARY_SECOND => 'الصف الثاني الابتدائي',
            self::PRIMARY_THIRD => 'الصف الثالث الابتدائي',
            self::PRIMARY_FOURTH => 'الصف الرابع الابتدائي',
            self::PRIMARY_FIFTH => 'الصف الخامس الابتدائي',
            self::PRIMARY_SIXTH => 'الصف السادس الابتدائي',
            self::MIDDLE_FIRST => 'الصف الأول الإعدادي',
            self::MIDDLE_SECOND => 'الصف الثاني الإعدادي',
            self::MIDDLE_THIRD => 'الصف الثالث الإعدادي',
            self::SECONDARY_FIRST => 'الصف الأول الثانوي',
            self::SECONDARY_SECOND => 'الصف الثاني الثانوي',
            self::SECONDARY_THIRD => 'الصف الثالث الثانوي',
        };
    }

    public function gradeLevel(): int
    {
        return match ($this) {
            self::PRIMARY_FIRST => 1,
            self::PRIMARY_SECOND => 2,
            self::PRIMARY_THIRD => 3,
            self::PRIMARY_FOURTH => 4,
            self::PRIMARY_FIFTH => 5,
            self::PRIMARY_SIXTH => 6,
            self::MIDDLE_FIRST => 7,
            self::MIDDLE_SECOND => 8,
            self::MIDDLE_THIRD => 9,
            self::SECONDARY_FIRST => 10,
            self::SECONDARY_SECOND => 11,
            self::SECONDARY_THIRD => 12,
        };
    }

    public function stage(): string
    {
        return match ($this) {
            self::PRIMARY_FIRST,
            self::PRIMARY_SECOND,
            self::PRIMARY_THIRD,
            self::PRIMARY_FOURTH,
            self::PRIMARY_FIFTH,
            self::PRIMARY_SIXTH => 'ابتدائي',
            self::MIDDLE_FIRST,
            self::MIDDLE_SECOND,
            self::MIDDLE_THIRD => 'إعدادي',
            self::SECONDARY_FIRST,
            self::SECONDARY_SECOND,
            self::SECONDARY_THIRD => 'ثانوي',
        };
    }

    public static function primaryValues(): array
    {
        return array_map(fn(self $type) => $type->value, array_filter(self::cases(), fn(self $type) => $type->stage() === 'ابتدائي'));
    }

    public static function middleValues(): array
    {
        return array_map(fn(self $type) => $type->value, array_filter(self::cases(), fn(self $type) => $type->stage() === 'إعدادي'));
    }

    public static function secondaryValues(): array
    {
        return array_map(fn(self $type) => $type->value, array_filter(self::cases(), fn(self $type) => $type->stage() === 'ثانوي'));
    }
}
