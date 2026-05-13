<?php

namespace App\Models\Grading;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Academic\StudentEnrollment;
use App\Models\Subjects\Subject;
use App\Models\Subjects\SubjectComponent;
use App\Models\Subjects\Term;

/**
 * نموذج علامات الطالب
 * يخزن علامة الطالب في كل مكون من مكونات المادة
 * 
 * @property int $id
 * @property int $enrollment_id        FK → student_enrollments
 * @property int $subject_id           FK → subjects
 * @property int $component_id         FK → subject_components
 * @property int $term_id              FK → terms
 * @property float $mark               العلامة (0-100 أو حسب النطاق المحدد)
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read StudentEnrollment $enrollment
 * @property-read Subject $subject
 * @property-read SubjectComponent $component
 * @property-read Term $term
 */
class StudentMark extends Model
{
    use Filterable;

    protected $fillable = [
        'enrollment_id',
        'subject_id',
        'component_id',
        'term_id',
        'mark',
    ];

    protected $casts = [
        'mark' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * التسجيل الأكاديمي للطالب
     * 
     * @return BelongsTo
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    /**
     * المادة
     * 
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * مكون المادة (كتابي، شفهي...)
     * 
     * @return BelongsTo
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(SubjectComponent::class);
    }

    /**
     * الفصل الدراسي
     * 
     * @return BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    // ────── Scopes ──────

    /**
     * البحث حسب التسجيل
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $enrollmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEnrollment($query, int $enrollmentId)
    {
        return $query->where('enrollment_id', $enrollmentId);
    }

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
     * البحث حسب الفصل
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $termId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTerm($query, int $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * البحث حسب المكون
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $componentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForComponent($query, int $componentId)
    {
        return $query->where('component_id', $componentId);
    }

    /**
     * العلامات أعلى من حد معين
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $minMark
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAbove($query, float $minMark)
    {
        return $query->where('mark', '>=', $minMark);
    }

    /**
     * العلامات أقل من حد معين
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param float $maxMark
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBelow($query, float $maxMark)
    {
        return $query->where('mark', '<', $maxMark);
    }

    // ────── Methods ──────

    /**
     * حساب النسبة المئوية للعلامة
     * 
     * @return float
     */
    public function getPercentage(): float
    {
        if ($this->component->out_of === 0) {
            return 0;
        }

        return round(($this->mark / $this->component->out_of) * 100, 2);
    }

    /**
     * التحقق من أن العلامة ناجحة (بناءً على حد النجاح للمادة)
     * 
     * @return bool
     */
    public function isPassing(): bool
    {
        return $this->mark >= $this->subject->pass_mark;
    }

    /**
     * التحقق من أن العلامة صحيحة (ضمن النطاق المسموح)
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->mark >= 0 && $this->mark <= $this->component->out_of;
    }

    // ────── Accessors ──────

    /**
     * النسبة المئوية للعلامة
     * 
     * @return string
     */
    public function getPercentageDisplayAttribute(): string
    {
        return round($this->getPercentage(), 2) . '%';
    }

    /**
     * العلامة مع النطاق
     * 
     * @return string
     */
    public function getMarkDisplayAttribute(): string
    {
        return round($this->mark, 2) . ' / ' . $this->component->out_of;
    }

    /**
     * حالة العلامة (ناجح/راسب)
     * 
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->isPassing() ? 'ناجح ✅' : 'راسب ❌';
    }

    /**
     * تفاصيل العلامة
     * 
     * @return string
     */
    public function getDetailAttribute(): string
    {
        return "{$this->component->name}: {$this->mark_display} ({$this->percentage_display})";
    }
}
