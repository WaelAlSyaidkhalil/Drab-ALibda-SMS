<?php

namespace App\Models\Grading;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Academic\StudentEnrollment;
use App\Models\Subjects\Subject;
use App\Models\Subjects\SubjectComponent;
use App\Models\Subjects\Term;
use App\Observers\Admin\StudentSubjectResultObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * نموذج علامات الطالب
 *
 * يخزن علامة الطالب في مكوّن محدد من المادة
 * ضمن فصل دراسي محدد.
 *
 * نظام العلامات:
 * - كتابي: من 50
 * - شفهي: من 30
 * - وظائف / عملي: من 20
 *
 * مجموع مكونات الفصل = 100
 */
#[ObservedBy([StudentSubjectResultObserver::class])]
class StudentMark extends Model
{
    use Filterable;

    protected $fillable = [
        'enrollment_id',
        'subject_id',
        'subject_component_id',
        'term_id',
        'mark',
    ];

    protected $casts = [
        'mark' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─────────────────────────────────────────────
    // العلاقات
    // ─────────────────────────────────────────────

    /**
     * التسجيل الأكاديمي للطالب.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    /**
     * المادة.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * مكوّن المادة.
     *
     * مثال:
     * - Written
     * - Oral
     * - Practical / Homework
     */
    public function subjectComponent(): BelongsTo
    {
        return $this->belongsTo(SubjectComponent::class);
    }

    /**
     * الفصل الدراسي.
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    // ─────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────

    public function scopeForEnrollment($query, int $enrollmentId)
    {
        return $query->where('enrollment_id', $enrollmentId);
    }

    public function scopeForSubject($query, int $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForTerm($query, int $termId)
    {
        return $query->where('term_id', $termId);
    }

    public function scopeForComponent($query, int $componentId)
    {
        return $query->where('subject_component_id', $componentId);
    }

    public function scopeAbove($query, float $minMark)
    {
        return $query->where('mark', '>=', $minMark);
    }

    public function scopeBelow($query, float $maxMark)
    {
        return $query->where('mark', '<', $maxMark);
    }

    // ─────────────────────────────────────────────
    // Methods
    // ─────────────────────────────────────────────

    /**
     * الحد الأعلى لعلامة هذا المكوّن.
     *
     * يتم أخذه من subject_components.out_of.
     *
     * المتوقع:
     * - Written    => 50
     * - Oral       => 30
     * - Practical  => 20
     */
    public function getMaxMark(): float
    {
        return (float) ($this->subjectComponent?->out_of ?? 0);
    }

    /**
     * حساب النسبة المئوية للعلامة.
     *
     * مثال:
     * 35 / 50 = 70%
     * 24 / 30 = 80%
     * 18 / 20 = 90%
     */
    public function getPercentage(): float
    {
        $maxMark = $this->getMaxMark();

        if ($maxMark <= 0) {
            return 0;
        }

        return round(($this->mark / $maxMark) * 100, 2);
    }

    /**
     * التحقق من صحة العلامة.
     *
     * العلامة يجب أن تكون:
     *
     * 0 <= mark <= out_of
     *
     * مثال:
     * الكتابي:
     * 35 / 50 => صحيح
     *
     * الشفهي:
     * 25 / 30 => صحيح
     *
     * الوظائف:
     * 18 / 20 => صحيح
     *
     * أما:
     * 40 / 30 => غير صحيحة
     */
    public function isValid(): bool
    {
        $maxMark = $this->getMaxMark();

        if ($maxMark <= 0) {
            return false;
        }

        return $this->mark >= 0
            && $this->mark <= $maxMark;
    }

    /**
     * الحصول على اسم المكوّن.
     */
    public function getComponentName(): string
    {
        return $this->subjectComponent?->description
            ?? $this->subjectComponent?->type?->value
            ?? 'مكوّن';
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    /**
     * النسبة المئوية للعلامة.
     */
    public function getPercentageDisplayAttribute(): string
    {
        return $this->getPercentage() . '%';
    }

    /**
     * العلامة مع الحد الأعلى.
     *
     * مثال:
     * 35 / 50
     */
    public function getMarkDisplayAttribute(): string
    {
        return round($this->mark, 2)
            . ' / '
            . $this->getMaxMark();
    }

    /**
     * تفاصيل العلامة.
     *
     * مثال:
     * الكتابي: 35 / 50 (70%)
     */
    public function getDetailAttribute(): string
    {
        return "{$this->getComponentName()}: "
            . "{$this->mark_display} "
            . "({$this->percentage_display})";
    }
}