<?php

namespace App\Enums;

/**
 * الجنس
 * 
 * @package App\Enums
 */
enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    /**
     * الوصف البشري للجنس
     */
    public function label(): string
    {
        return match($this) {
            self::MALE => __('dashboard.enums.gender.male'),
            self::FEMALE => __('dashboard.enums.gender.female'),
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
