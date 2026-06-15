<?php

namespace App\Models\Academic;

use App\Models\Subjects\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\Filterable;
use Illuminate\Support\Carbon;

/**
 * نموذج الصف الدراسي
 * يمثل الصفوف الدراسية (الأول الابتدائي، الثاني الإعدادي... إلخ)
 *
 * @property int $id
 * @property string $name              اسم الصف (الأول الابتدائي، الثاني الإعدادي)
 * @property int $grade_level          مستوى الصف (1-12)
 * @property string|null $description  وصف إضافي
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection $sections
 * @property-read Collection $subjects
 */
class SchoolClass extends Model
{
    use Filterable;

    protected $table = 'classes'; // اسم الجدول (لتجنب تضارب مع كلمة Class المحجوزة)

    protected $fillable = [
        'name',
        'grade_level',
        'description',
    ];

    protected $casts = [
        'grade_level' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * جميع الشعب التابعة لهذا الصف
     *
     * @return HasMany
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    /**
     * المواد التي تُدرس في هذا الصف
     *
     * @return BelongsToMany
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject',
            'class_id',
            'subject_id'
        );
    }

    // ────── Scopes ──────

    /**
     * البحث حسب مستوى الصف
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $level
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGradeLevel($query, int $level)
    {
        return $query->where('grade_level', $level);
    }

    /**
     * الصفوف الابتدائية (1-6)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrimary($query)
    {
        return $query->whereBetween('grade_level', [1, 6]);
    }

    /**
     * الصفوف الإعدادية (7-9)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMiddle($query)
    {
        return $query->whereBetween('grade_level', [7, 9]);
    }

    /**
     * الصفوف الثانوية (10-12)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSecondary($query)
    {
        return $query->whereBetween('grade_level', [10, 12]);
    }

    // ────── Accessors ──────

    /**
     * عدد الشعب في هذا الصف
     *
     * @return int
     */
    public function getSectionCountAttribute(): int
    {
        return $this->sections()->count();
    }

    /**
     * عدد الطلاب في هذا الصف (جميع الشعب)
     *
     * @return int
     */
    public function getStudentCountAttribute(): int
    {
        return StudentEnrollment::whereHas('section', function ($q) {
            $q->where('class_id', $this->id);
        })
        ->where('status', 'active')
        ->count();
    }
}
