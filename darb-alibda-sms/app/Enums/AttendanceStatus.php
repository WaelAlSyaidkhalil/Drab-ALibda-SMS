<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';

    public function label(): string
    {
        return match($this) {
            self::PRESENT => __('dashboard.enums.attendance_status.present'),
            self::ABSENT => __('dashboard.enums.attendance_status.absent'),
            self::LATE => __('dashboard.enums.attendance_status.late'),
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
            'success' => self::PRESENT,
            'danger' => self::ABSENT,
            'warning' => self::LATE,
        ];
    }
}