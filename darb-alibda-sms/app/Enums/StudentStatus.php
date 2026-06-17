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

    /**
     * الوصف البشري للحالة
     */
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'مسجّل حالياً',
            self::PROMOTED => 'نجح وانتقل',
            self::REPEATED => 'إعادة سنة',
            self::TRANSFERRED => 'انتقل لمدرسة أخرى',
            self::GRADUATED => 'تخرج',
            self::WITHDRAWN => 'انسحب',
        };
    }

    /**
     * الحالات النشطة (الطالب لا يزال مسجلاً)
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * الحالات النهائية (الطالب لن يعود)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::TRANSFERRED, self::GRADUATED, self::WITHDRAWN]);
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
