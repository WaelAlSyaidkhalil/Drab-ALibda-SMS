<?php

namespace App\Enums;

enum SubjectComponentType: string
{
    case WRITTEN = 'written';
    case ORAL = 'oral';
    case PRACTICAL = 'practical';


    public function label(): string
    {
        return match ($this) {
            self::WRITTEN => __('dashboard.enums.subject_component_type.written'),
            self::ORAL => __('dashboard.enums.subject_component_type.oral'),
            self::PRACTICAL => __('dashboard.enums.subject_component_type.practical'),
        };
    }

    public static function getColors(): array
    {
        return [
            'danger' => self::WRITTEN,
            'success' => self::ORAL,
            'info' => self::PRACTICAL,
        ];
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