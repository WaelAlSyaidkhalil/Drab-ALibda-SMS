<?php

namespace App\Enums;

/**
 * نتائج علامات الطالب في المواد
 * 
 * @package App\Enums
 */
enum MarkResult: string
{
    case PASS = 'pass';       // ناجح
    case FAIL = 'fail';       // راسب
    case PENDING = 'pending'; // قيد الانتظار

    /**
     * الوصف البشري للنتيجة
     */
    public function label(): string
    {
        return match($this) {
            self::PASS => 'ناجح ✅',
            self::FAIL => 'راسب ❌',
            self::PENDING => 'قيد الانتظار ⏳',
        };
    }

    /**
     * اللون المناسب للنتيجة (للـ UI)
     */
    public function color(): string
    {
        return match($this) {
            self::PASS => 'green',
            self::FAIL => 'red',
            self::PENDING => 'yellow',
        };
    }

    /**
     * هل النتيجة نهائية؟
     */
    public function isFinal(): bool
    {
        return $this !== self::PENDING;
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->value,
            ])
            ->toArray();
    }
}
