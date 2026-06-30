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

    public function label(): string
    {
        return match($this) {
            self::Pending => __('dashboard.enums.suggestion_status.pending'),
            self::Reviewed => __('dashboard.enums.suggestion_status.reviewed'),
            self::Accepted => __('dashboard.enums.suggestion_status.accepted'),
            self::Rejected => __('dashboard.enums.suggestion_status.rejected'),
        };
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
            'warning' => self::Pending,
            'info' => self::Reviewed,
            'success' => self::Accepted,
            'danger' => self::Rejected,
        ];
    }
}