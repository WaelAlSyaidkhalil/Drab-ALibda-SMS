<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait للعمل مع الحالات (Status)
 * يوفر scopes للتصفية حسب الحالة
 * 
 * @package App\Models\Traits
 */
trait HasStatus
{
    /**
     * البحث حسب الحالة
     * 
     * @param Builder $query
     * @param string|array $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, string|array $status): Builder
    {
        if (is_array($status)) {
            return $query->whereIn('status', $status);
        }
        
        return $query->where('status', $status);
    }

    /**
     * الحالات النشطة فقط
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * الحالات غير النشطة
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->whereNot('status', 'active');
    }

   
}
