<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';

    /**
     * Get Arabic translation for the enum case
     */
    public function getArabic(): string
    {
        return match($this) {
            self::PRESENT => 'حاضر',
            self::ABSENT => 'غائب',
            self::LATE => 'متأخر',
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

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->value,
            ])
            ->toArray();
    }

    /**
     * Get color class for each status (useful for UI)
     */
    public function getColorClass(): string
    {
        return match($this) {
            self::PRESENT => 'success',
            self::ABSENT => 'danger',
            self::LATE => 'warning',
        };
    }

    /**
     * Get icon class for each status
     */
    public function getIconClass(): string
    {
        return match($this) {
            self::PRESENT => 'fas fa-check-circle',
            self::ABSENT => 'fas fa-times-circle',
            self::LATE => 'fas fa-clock',
        };
    }

    /**
     * Get badge HTML for display
     */
    public function getBadge(): string
    {
        $colors = [
            'present' => 'bg-success',
            'absent' => 'bg-danger',
            'late' => 'bg-warning',
        ];
        
        return sprintf(
            '<span class="badge %s">%s</span>',
            $colors[$this->value] ?? 'bg-secondary',
            $this->getArabic()
        );
    }

    /**
     * Check if status is present
     */
    public function isPresent(): bool
    {
        return $this === self::PRESENT;
    }

    /**
     * Check if status is absent
     */
    public function isAbsent(): bool
    {
        return $this === self::ABSENT;
    }

    /**
     * Check if status is late
     */
    public function isLate(): bool
    {
        return $this === self::LATE;
    }

    /**
     * Get all statuses grouped by type
     */
    public static function getGrouped(): array
    {
        return [
            'positive' => [
                self::PRESENT->value => self::PRESENT->getArabic(),
            ],
            'negative' => [
                self::ABSENT->value => self::ABSENT->getArabic(),
            ],
            'neutral' => [
                self::LATE->value => self::LATE->getArabic(),
            ],
        ];
    }
}