<?php

namespace App\Enums;

enum AudienceType: string
{
    case ALL = 'all';
    case TEACHERS = 'teachers';
    case PARENTS = 'parents';
    case STUDENTS = 'students';

    /**
     * Get Arabic translation for the enum case
     */
    public function getArabic(): string
    {
        return match($this) {
            self::ALL => 'الكل',
            self::TEACHERS => 'المعلمين',
            self::PARENTS => 'أولياء الأمور',
            self::STUDENTS => 'الطلاب',
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

    /**
     * Get color class for each audience type
     */
    public function getColorClass(): string
    {
        return match($this) {
            self::ALL => 'primary',
            self::TEACHERS => 'success',
            self::PARENTS => 'warning',
            self::STUDENTS => 'info',
        };
    }

    /**
     * Get icon class for each audience type
     */
    public function getIconClass(): string
    {
        return match($this) {
            self::ALL => 'fas fa-users',
            self::TEACHERS => 'fas fa-chalkboard-teacher',
            self::PARENTS => 'fas fa-user-friends',
            self::STUDENTS => 'fas fa-user-graduate',
        };
    }

    /**
     * Get badge HTML for display
     */
    public function getBadge(): string
    {
        return sprintf(
            '<span class="badge bg-%s"><i class="%s"></i> %s</span>',
            $this->getColorClass(),
            $this->getIconClass(),
            $this->getArabic()
        );
    }

    /**
     * Check if audience is 'all'
     */
    public function isAll(): bool
    {
        return $this === self::ALL;
    }

    /**
     * Check if audience is for teachers
     */
    public function isTeachers(): bool
    {
        return $this === self::TEACHERS;
    }

    /**
     * Check if audience is for parents
     */
    public function isParents(): bool
    {
        return $this === self::PARENTS;
    }

    /**
     * Check if audience is for students
     */
    public function isStudents(): bool
    {
        return $this === self::STUDENTS;
    }

    /**
     * Check if it's a specific audience (not 'all')
     */
    public function isSpecific(): bool
    {
        return !$this->isAll();
    }

    /**
     * Get all audience types for dropdown (with labels)
     */
    public static function getSelectOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[] = [
                'value' => $case->value,
                'label' => $case->getArabic(),
                'color' => $case->getColorClass(),
                'icon' => $case->getIconClass(),
            ];
        }
        return $options;
    }

    /**
     * Get only specific audience types (excluding 'all')
     */
    public static function getSpecificTypes(): array
    {
        return array_filter(self::cases(), function ($case) {
            return !$case->isAll();
        });
    }

    /**
     * Get audience types for a specific user role
     */
    public static function getForRole(string $role): array
    {
        return match($role) {
            'admin' => self::cases(),
            'teacher' => [self::ALL, self::TEACHERS],
            'parent' => [self::ALL, self::PARENTS],
            'student' => [self::ALL, self::STUDENTS],
            default => [self::ALL],
        };
    }
}