<?php

namespace App\Enums;

enum TermType: string
{
    case FIRST_TERM = 'First_Term';
    case SECOND_TERM = 'Second_Term';

    public function label(): string
    {
        return match ($this) {
            self::FIRST_TERM => __('dashboard.enums.term_type.first_term'),
            self::SECOND_TERM => __('dashboard.enums.term_type.second_term'),
        };
    }

    public function number(): int
    {
        return match ($this) {
            self::FIRST_TERM => 1,
            self::SECOND_TERM => 2,
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
}
