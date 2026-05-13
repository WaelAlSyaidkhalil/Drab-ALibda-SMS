<?php

namespace App\Enums;

/**
 * أدوار المستخدمين في النظام
 * 
 * @package App\Enums
 */
enum UserRole: string
{
    // الإدارة
    case ADMIN = 'admin';
    
    // الطلاب وأولياء الأمور
    case STUDENT = 'student';
    case PARENT = 'parent';
    
    // الموظفين
    case TEACHER = 'teacher';
    
    /**
     * الوصف البشري للدور
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'مسؤول النظام',
            self::STUDENT => 'طالب',
            self::PARENT => 'ولي أمر',
            self::TEACHER => 'معلم',
        };
    }

    /**
     * الأدوار التي لها صلاحيات مرتفعة
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * الأدوار التعليمية (معلم + طالب)
     */
    public function isEducational(): bool
    {
        return in_array($this, [self::TEACHER, self::STUDENT]);
    }
}
