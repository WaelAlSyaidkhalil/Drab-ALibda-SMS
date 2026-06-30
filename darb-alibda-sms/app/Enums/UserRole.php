<?php

namespace App\Enums;

/**
 * أدوار المستخدمين في النظام
 * 
 * @package App\Enums
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';
    case PARENT = 'parent';
    case TEACHER = 'teacher';
    
    public function label(): string
    {
        return match($this) {
            self::ADMIN => __('dashboard.enums.user_role.admin'),
            self::STUDENT => __('dashboard.enums.user_role.student'),
            self::PARENT => __('dashboard.enums.user_role.parent'),
            self::TEACHER => __('dashboard.enums.user_role.teacher'),
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getColors(): array
    {
        return [
            'success' => UserRole::ADMIN,
            'warning' => UserRole::STUDENT,
            'info' => UserRole::TEACHER,
            'danger' => UserRole::PARENT,
        ];
    }
}
