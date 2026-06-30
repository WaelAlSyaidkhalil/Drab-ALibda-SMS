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

    public function label(): string
    {
        return match($this) {
            self::PENDING => __('dashboard.enums.complaint_status.pending'),
            self::IN_PROGRESS => __('dashboard.enums.complaint_status.in_progress'),
            self::RESOLVED => __('dashboard.enums.complaint_status.resolved'),
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
            'warning' => self::PENDING,
            'info' => self::IN_PROGRESS,
            'success' => self::RESOLVED,
        ];
    }
}