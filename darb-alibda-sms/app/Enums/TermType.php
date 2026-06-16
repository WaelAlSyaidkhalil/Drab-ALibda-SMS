<?php

namespace App\Enums;

enum TermType: string
{
    case FIRST_TERM = 'First_Term';
    case SECOND_TERM = 'Second_Term';

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
