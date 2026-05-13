<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait لحساب الأسماء الكاملة
 * 
 * @package App\Models\Traits
 */
trait HasFullName
{
    /**
     * الحصول على الاسم الكامل (first_name + last_name)
     * 
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        $parts = [];
        
        if (!empty($this->first_name)) {
            $parts[] = $this->first_name;
        }
        
        if (!empty($this->last_name)) {
            $parts[] = $this->last_name;
        }
        
        return implode(' ', $parts) ?: 'بدون اسم';
    }

    /**
     * البحث حسب الاسم الكامل
     * 
     * @param Builder $query
     * @param string $name
     * @return Builder
     */
    public function scopeByFullName(Builder $query, string $name): Builder
    {
        return $query->where(function (Builder $q) use ($name) {
            $q->where('first_name', 'like', "%{$name}%")
              ->orWhere('last_name', 'like', "%{$name}%");
        });
    }

    /**
     * البحث حسب الاسم الأول
     * 
     * @param Builder $query
     * @param string $firstName
     * @return Builder
     */
    public function scopeByFirstName(Builder $query, string $firstName): Builder
    {
        return $query->where('first_name', 'like', "%{$firstName}%");
    }

    /**
     * البحث حسب الاسم الأخير
     * 
     * @param Builder $query
     * @param string $lastName
     * @return Builder
     */
    public function scopeByLastName(Builder $query, string $lastName): Builder
    {
        return $query->where('last_name', 'like', "%{$lastName}%");
    }
}
