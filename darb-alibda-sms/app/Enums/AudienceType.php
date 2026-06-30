<?php

namespace App\Enums;

enum AudienceType: string
{
    case ALL = 'all';
    case TEACHERS = 'teachers';
    case PARENTS = 'parents';
    case STUDENTS = 'students';

    public function label(): string
    {
        return match ($this) {
            self::ALL => __('dashboard.enums.audience_types.all'),
            self::TEACHERS => __('dashboard.enums.audience_types.teachers'),
            self::PARENTS => __('dashboard.enums.audience_types.parents'),
            self::STUDENTS => __('dashboard.enums.audience_types.students'),
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getColors(): array
    {
        return [
            'success' => self::ALL,
            'primary' => self::TEACHERS,
            'warning' => self::PARENTS,
            'info' => self::STUDENTS,
        ];
    }


    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }

}