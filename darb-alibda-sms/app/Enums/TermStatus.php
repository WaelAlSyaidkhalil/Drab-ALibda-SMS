<?php

namespace App\Enums;

enum TermStatus: string
{
    case ACTIVE = 'active';
    case UPCOMING = 'upcoming';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('dashboard.enums.term_status.active'),
            self::UPCOMING => __('dashboard.enums.term_status.upcoming'),
            self::COMPLETED => __('dashboard.enums.term_status.completed'),
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
                $case->value => $case->label()
            ])
            ->toArray();
    }

    public static function getColors(): array
    {
        return [
            'success' => self::ACTIVE,
            'warning' => self::UPCOMING,
            'gray' => self::COMPLETED,
        ];
    }
}