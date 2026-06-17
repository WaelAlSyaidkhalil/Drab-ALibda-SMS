<?php

namespace App\Enums;

enum SubjectComponentType: string
{
    case WRITTEN = 'written';
    case ORAL = 'oral';
    case PRACTICAL = 'practical';

    /**
     * Get Arabic translation for the enum case
     */
    public function getArabic(): string
    {
        return match($this) {
            self::WRITTEN => 'تحريري',
            self::ORAL => 'شفهي',
            self::PRACTICAL => 'عملي',
        };
    }

    /**
     * Get all cases with their Arabic translations
     */
    public static function getWithArabic(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->getArabic();
        }
        return $result;
    }

    /**
     * Get all case values
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}