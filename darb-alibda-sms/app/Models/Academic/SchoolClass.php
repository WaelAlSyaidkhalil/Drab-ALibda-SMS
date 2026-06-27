<?php

namespace App\Models\Academic;

use App\Enums\ClassType;
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
 * @property ClassType $type           نوع الصف (primary_first ... secondary_third)
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
        'type',
    ];

    protected $casts = [
        'type' => ClassType::class,
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
        $types = array_filter(ClassType::cases(), fn (ClassType $type) => $type->getGradeLevel() === $level);
        return $query->whereIn('type', array_map(fn (ClassType $type) => $type->value, $types));
    }

    /**
     * الصفوف الابتدائية (1-6)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrimary($query)
    {
        return $query->whereIn('type', ClassType::primaryValues());
    }

    /**
     * الصفوف الإعدادية (7-9)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMiddle($query)
    {
        return $query->whereIn('type', ClassType::middleValues());
    }

    /**
     * الصفوف الثانوية (10-12)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSecondary($query)
    {
        return $query->whereIn('type', ClassType::secondaryValues());
    }

    // ────── Accessors ──────

    /**
     * اسم الصف بالعربية
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->type?->label() ?? 'غير معروف';
    }

    /**
     * مستوى الصف الرقمي
     *
     * @return int|null
     */
    public function getGradeLevelAttribute(): ?int
    {
        return $this->type?->getGradeLevel();
    }

    /**
     * المرحلة الدراسية
     *
     * @return string|null
     */
    public function getStageAttribute(): ?string
    {
        return $this->type?->stage();
    }

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

    public function getTypeName(): string
    {
        return $this->type?->label() ?? 'غير معروف';
    }
}
