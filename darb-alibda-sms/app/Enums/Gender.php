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
            self::MALE => 'ذكر',
            self::FEMALE => 'أنثى',
        };
    }

    /**
     * الضمير المناسب
     */
    public function pronoun(): string
    {
        return match($this) {
            self::MALE => 'هو',
            self::FEMALE => 'هي',
        };
    }
}
