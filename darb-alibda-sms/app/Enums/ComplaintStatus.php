<?php

namespace App\Enums;

enum ComplaintStatus : string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';

    public static function getValues(): array
    {
        return [
            self::PENDING,
            self::IN_PROGRESS,
            self::RESOLVED,
        ];
    }

    public static function label(string $status): string
    {
        return match($status) {
            self::PENDING => 'قيد الانتظار',
            self::IN_PROGRESS => 'قيد المعالجة',
            self::RESOLVED => 'تم الحل',
            default => 'غير معروف',
        };
    }
    
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->value,
            ])
            ->toArray();
    }
}