<?php

namespace App\Enums;

/**
 * حالات تسجيل الطالب في السنة الدراسية
 * 
 * @package App\Enums
 */
enum StudentStatus: string
{
    case ACTIVE = 'active';           // مسجّل حالياً
    case PROMOTED = 'promoted';       // نجح وانتقل للصف التالي
    case REPEATED = 'repeated';       // راسب يعيد السنة
    case TRANSFERRED = 'transferred'; // انتقل لمدرسة أخرى
    case GRADUATED = 'graduated';     // تخرج
    case WITHDRAWN = 'withdrawn';     // انسحب

    
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => __('dashboard.enums.student_status.active'),
            self::PROMOTED => __('dashboard.enums.student_status.promoted'),
            self::REPEATED => __('dashboard.enums.student_status.repeated'),
            self::TRANSFERRED => __('dashboard.enums.student_status.transferred'),
            self::GRADUATED => __('dashboard.enums.student_status.graduated'),
            self::WITHDRAWN => __('dashboard.enums.student_status.withdrawn'),
        };
    }

    public static function getColors(): array
    {
        return [
            'success' => self::PROMOTED,
            'warning' => self::GRADUATED,
            'info' => self::ACTIVE,
            'gray' => self::REPEATED,
            'secondary' => self::TRANSFERRED,
            'danger' => self::WITHDRAWN,
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
