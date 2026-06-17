<?php

namespace App\Enums;

/**
 * أيام الأسبوع للجدول الدراسي
 * 
 * @package App\Enums
 */
enum DayOfWeek: string
{
    case SUNDAY = 'Sun';
    case MONDAY = 'Mon';
    case TUESDAY = 'Tue';
    case WEDNESDAY = 'Wed';
    case THURSDAY = 'Thu';

    /**
     * الوصف البشري لليوم
     */
    public function label(): string
    {
        return match($this) {
            self::SUNDAY => 'الأحد',
            self::MONDAY => 'الإثنين',
            self::TUESDAY => 'الثلاثاء',
            self::WEDNESDAY => 'الأربعاء',
            self::THURSDAY => 'الخميس',
        };
    }

    /**
     * يوم نهاية الأسبوع؟
     */
    public function isWeekend(): bool
    {
        return false; // في النظام العربي لا يوجد عطلة أسبوعية وسط الأسبوع
    }

    /**
     * رقم اليوم (1-5)
     */
    public function dayNumber(): int
    {
        return match($this) {
            self::SUNDAY => 1,
            self::MONDAY => 2,
            self::TUESDAY => 3,
            self::WEDNESDAY => 4,
            self::THURSDAY => 5,
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
