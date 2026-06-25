<?php

namespace App\Enums;

enum SuggestionStatus: string
{
    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public static function getValues(): array
    {
        return [
            self::Pending,
            self::Reviewed,
            self::Accepted,
            self::Rejected,
        ];
    }

    public static function label(string $status): string
    {
        return match($status) {
            self::Pending => 'قيد الانتظار',
            self::Reviewed => 'تمت المراجعة',
            self::Accepted => 'تم القبول',
            self::Rejected => 'تم الرفض',
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