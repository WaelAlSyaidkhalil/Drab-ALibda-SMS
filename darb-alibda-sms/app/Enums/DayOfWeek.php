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

    public function label(): string
    {
        return match($this) {
            self::SUNDAY => __('dashboard.enums.day_of_week.sunday'),
            self::MONDAY => __('dashboard.enums.day_of_week.monday'),
            self::TUESDAY => __('dashboard.enums.day_of_week.tuesday'),
            self::WEDNESDAY => __('dashboard.enums.day_of_week.wednesday'),
            self::THURSDAY => __('dashboard.enums.day_of_week.thursday'),
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }

    public static function getColors(): array
    {
        return [
            'danger' => self::SUNDAY,
            'gray' => self::MONDAY,
            'success' => self::TUESDAY,
            'warning' => self::WEDNESDAY,
            'info' => self::THURSDAY,
        ];
    }
}
