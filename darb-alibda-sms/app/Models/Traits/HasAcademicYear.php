<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Trait للعمل مع السنوات الدراسية
 * 
 * @package App\Models\Traits
 */
trait HasAcademicYear
{
    /**
     * البحث حسب السنة الدراسية
     * 
     * @param Builder $query
     * @param string $year مثلاً "2025-2026"
     * @return Builder
     */
    public function scopeForYear(Builder $query, string $year): Builder
    {
        return $query->where('academic_year', $year);
    }

    /**
     * السنة الدراسية الحالية
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeCurrentYear(Builder $query): Builder
    {
        $currentYear = $this->getCurrentAcademicYear();
        return $query->where('academic_year', $currentYear);
    }

    /**
     * حساب السنة الدراسية الحالية
     * (يبدأ من سبتمبر إلى أغسطس من السنة التالية)
     * 
     * @return string مثلاً "2025-2026"
     */
    public static function getCurrentAcademicYear(): string
    {
        $now = Carbon::now();
        $year = $now->year;
        
        // إذا كانت الشهور من 1 إلى 8 (يناير إلى أغسطس)
        // فالسنة الدراسية تبدأ من السنة الماضية
        if ($now->month <= 8) {
            $year--;
        }
        
        return "{$year}-" . ($year + 1);
    }

    /**
     * الفصل الدراسي الحالي في السنة الحالية
     * (الفصل الأول: سبتمبر - ديسمبر، الفصل الثاني: يناير - أغسطس)
     * 
     * @return int (1 or 2)
     */
    public static function getCurrentTerm(): int
    {
        $month = Carbon::now()->month;
        
        // الفصل الأول: 9-12 (سبتمبر - ديسمبر)
        // الفصل الثاني: 1-8 (يناير - أغسطس)
        return $month >= 9 ? 1 : 2;
    }
}
