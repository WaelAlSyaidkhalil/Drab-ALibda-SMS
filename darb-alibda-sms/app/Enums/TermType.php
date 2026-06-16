<?php

namespace App\Enums;

enum TermType: string
{
    case FIRST_TERM = 'first_term';
    case SECOND_TERM = 'second_term';

    public function label(): string
    {
        return match ($this) {
            self::FIRST_TERM => 'الفصل الأول',
            self::SECOND_TERM => 'الفصل الثاني',
        };
    }

    public function number(): int
    {
        return match ($this) {
            self::FIRST_TERM => 1,
            self::SECOND_TERM => 2,
        };
    }
}
