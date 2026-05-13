<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Filterable;

/**
 * نموذج الأدوار (الصلاحيات)
 * يمثل أدوار المستخدمين في النظام (إدارة، معلم، طالب، ولي أمر)
 * 
 * @property int $id
 * @property string $name              الاسم (admin, teacher, student, parent)
 * @property string|null $description  وصف الدور
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection $users
 */
class Role extends Model
{
    use Filterable;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * جميع المستخدمين الذين لديهم هذا الدور
     * 
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ────── Scopes ──────

    /**
     * البحث حسب اسم الدور
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    /**
     * الأدوار الإدارية (admin, staff)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin($query)
    {
        return $query->whereIn('name', ['admin']);
    }

    /**
     * الأدوار التعليمية (teacher, student)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEducational($query)
    {
        return $query->whereIn('name', ['teacher','parent', 'student']);
    }

}
