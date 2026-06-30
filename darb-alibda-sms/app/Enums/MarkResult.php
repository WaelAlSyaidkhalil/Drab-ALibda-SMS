<?php

namespace App\Enums;

/**
 * نتائج علامات الطالب في المواد
 * 
 * @package App\Enums
 */
enum MarkResult: string
{
    case PASS = 'pass';       
    case FAIL = 'fail';       
    case PENDING = 'pending'; 


    public function label(): string
    {
        return match($this) {
            self::PASS => __('dashboard.enums.mark_result.pass'),
            self::FAIL => __('dashboard.enums.mark_result.fail'),
            self::PENDING => __('dashboard.enums.mark_result.pending'),
        };
    }

    public static function getColors(): array
    {
        return [
            'success' => self::PASS,
            'danger' => self::FAIL,
            'warning' => self::PENDING,
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
