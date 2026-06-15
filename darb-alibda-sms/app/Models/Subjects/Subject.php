<?php

namespace App\Models\Subjects;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\Filterable;
use App\Models\Academic\SchoolClass;
use App\Models\Subjects\SubjectComponent;
use Illuminate\Support\Carbon;

/**
 * نموذج المادة الدراسية
 *
 * @property int $id
 * @property string $name              اسم المادة (رياضيات، عربي...)
 * @property string|null $description  وصف المادة
 * @property int|null $pass_mark       الحد الأدنى للنجاح (افتراضياً 50)
 * @property string|null $code         رمز المادة (MAT001)
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection $components
 * @property-read Collection $schoolClasses
 * @property-read Collection $terms
 */
class Subject extends Model
{
    use Filterable;

    protected $fillable = [
        'name',
        'description',
        'pass_mark',
        'code',
    ];

    protected $casts = [
        'pass_mark' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * مكونات هذه المادة (كتابي، شفهي، وظائف...)
     *
     * @return HasMany
     */
    public function components(): HasMany
    {
        return $this->hasMany(SubjectComponent::class);
    }

    /**
     * الصفوف التي تُدرس فيها هذه المادة
     *
     * @return BelongsToMany
     */
    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'class_subject',
            'subject_id',
            'class_id'
        );
    }

    /**
     * الفصول الدراسية التي تُدرس فيها هذه المادة
     *
     * @return BelongsToMany
     */
    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(
            Term::class,
            'term_subject',
            'subject_id',
            'term_id'
        );
    }

    // ────── Scopes ──────

    /**
     * البحث حسب الاسم
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * البحث حسب الرمز
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * المواد الأساسية (اللغات والرياضيات والعلوم)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCore($query)
    {
        return $query->whereIn('name', [
            'رياضيات',
            'لغة عربية',
            'لغة إنجليزية',
            'العلوم',
        ]);
    }

    // ────── Methods ──────

    /**
     * الحصول على عدد المكونات
     *
     * @return int
     */
    public function getComponentCount(): int
    {
        return $this->components()->count();
    }

    /**
     * الحصول على مجموع درجات المكونات
     *
     * @return float
     */
    public function getTotalComponentMarks(): float
    {
        return $this->components()
            ->sum('out_of');
    }

    // ────── Accessors ──────

    /**
     * حد النجاح الافتراضي أو المخصص
     *
     * @return int
     */
    public function getPassMarkAttribute(): int
    {
        return $this->attributes['pass_mark'] ?? 50;
    }

    /**
     * نسبة حد النجاح (%)
     *
     * @return float
     */
    public function getPassPercentageAttribute(): float
    {
        $total = $this->getTotalComponentMarks();
        return $total > 0 ? round(($this->pass_mark / $total) * 100, 2) : 0;
    }
}
