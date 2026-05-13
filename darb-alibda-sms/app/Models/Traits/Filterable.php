<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait للبحث والتصفية المتقدمة
 * يوفر scopes للاستعلامات الشائعة
 *
 * @package App\Models\Traits
 */
trait Filterable
{
    /**
     * البحث في حقل معين
     *
     * @param Builder $query
     * @param string $field اسم الحقل
     * @param string $value قيمة البحث
     * @return Builder
     */
    public function scopeWhereField(Builder $query, string $field, string $value): Builder
    {
        return $query->where($field, 'like', "%{$value}%");
    }

    /**
     * البحث في عدة حقول
     *
     * @param Builder $query
     * @param array $fields
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, array $fields, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($fields, $search) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'like', "%{$search}%");
            }
        });
    }

    /**
     * الترتيب حسب حقل معين
     *
     * @param Builder $query
     * @param string $field
     * @param string $direction (asc|desc)
     * @return Builder
     */
    public function scopeOrderByField(Builder $query, string $field, string $direction = 'asc'): Builder
    {
        return $query->orderBy($field, $direction);
    }

    /**
     * البحث ضمن نطاق معين
     *
     * @param Builder $query
     * @param string $field
     * @param mixed $start
     * @param mixed $end
     * @return Builder
     */
    public function scopeBetween(Builder $query, string $field, mixed $start, mixed $end): Builder
    {
        return $query->whereBetween($field, [$start, $end]);
    }

    /**
     * التصفية حسب حالة
     *
     * @param Builder $query
     * @param string $status
     * @return Builder
     */
    public function scopeFilterStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
