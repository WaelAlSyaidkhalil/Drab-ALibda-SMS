<?php

namespace App\Models\Subjects;

use App\Enums\SubjectComponentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Filterable;
use App\Models\Grading\StudentMark;
use App\Models\Subjects\Subject;

/**
 * نموذج مكون المادة
 * يمثل أنواع التقييم للمادة (كتابي، شفهي، وظائف... إلخ)
 * 
 * @property int $id
 * @property string|null $description  وصف المكون
 * @property int $subject_id           FK → subjects
 * @property SubjectComponent $type              اسم المكون (كتابي، شفهي، وظائف)
 * @property float $out_of             الدرجة العليا (مثلاً 20)
 * @property int $order                    ترتيب المكون في قائمة المكونات
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read Subject $subject
 * @property-read \Illuminate\Database\Eloquent\Collection $marks
 */
class SubjectComponent extends Model
{
    use Filterable;

    protected $fillable = [
        'subject_id',
        'description',
        'type',
        'out_of',
        'order',
    ];

    protected $casts = [
        'out_of' => 'float',
        'type' => SubjectComponentType::class,
        'order' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * المادة الأب
     * 
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * جميع علامات الطلاب في هذا المكون
     * 
     * @return HasMany
     */
    public function marks(): HasMany
    {
        return $this->hasMany(StudentMark::class);
    }

    // ────── Scopes ──────

    /**
     * البحث حسب المادة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $subjectId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * الترتيب حسب الأولوية
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }


    // ────── Accessors ──────

    /**
     * عرض الدرجة الكاملة
     * 
     * @return string
     */
    public function getOutOfDisplayAttribute(): string
    {
        return "من {$this->out_of}";
    }

    /**
     * تفاصيل المكون
     * 
     * @return string
     */
    public function getDetailAttribute(): string
    {
        return "{$this->type} - {$this->out_of_display}";
    }


    // ────── Methods ──────

    /**
     * Get the components for this subject
     */
    public function components(): HasMany
    {
        return $this->hasMany(SubjectComponent::class);
    }

    /**
     * Check if the sum of component marks equals the full mark
     */
    public function isComponentsSumValid(): bool
    {
        $totalComponentsMarks = $this->components()->sum('out_of');
        return $totalComponentsMarks == $this->full_mark;
    }

    /**
     * Get the sum of component marks
     */
    public function getComponentsTotalAttribute(): int
    {
        return $this->components()->sum('out_of');
    }

    /**
     * Get the difference between full mark and components total
     */
    public function getComponentsDifferenceAttribute(): int
    {
        return $this->full_mark - $this->getComponentsTotalAttribute();
    }

    /**
     * Check if components total is greater than full mark
     */
    public function isComponentsSumExceeding(): bool
    {
        return $this->getComponentsTotalAttribute() > $this->full_mark;
    }

    /**
     * Check if components total is less than full mark
     */
    public function isComponentsSumLess(): bool
    {
        return $this->getComponentsTotalAttribute() < $this->full_mark;
    }

}
