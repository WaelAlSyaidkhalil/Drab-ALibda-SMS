<?php

namespace App\Models\Grading;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Academic\StudentEnrollment;
use App\Models\Subjects\Subject;
use App\Enums\MarkResult;
use App\Observers\Admin\StudentSubjectResultObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * نموذج نتائج الطالب في المواد
 * يمثل كشف العلامات السنوي — يتم تحديثه تلقائياً من student_marks
 * 
 * @property int $id
 * @property int $enrollment_id        FK → enrollments
 * @property int $subject_id           FK → subjects
 * @property float|null $term1_mark    علامة الفصل الأول (مجموع المكونات)
 * @property float|null $term2_mark    علامة الفصل الثاني (مجموع المكونات)
 * @property float|null $yearly_mark   العلامة السنوية = متوسط الفصلين
 * @property string $result            النتيجة (pass, fail, pending)
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read StudentEnrollment $enrollment
 * @property-read Subject $subject
 */

#[ObservedBy([StudentSubjectResultObserver::class])]
class StudentSubjectResult extends Model
{
    use Filterable;

    protected $fillable = [
        'enrollment_id',
        'subject_id',
        'term1_mark',
        'term2_mark',
        'yearly_mark',
        'result',
    ];

    protected $casts = [
        'term1_mark' => 'float',
        'term2_mark' => 'float',
        'yearly_mark' => 'float',
        'result' => MarkResult::class,
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
     * النتائج الناجحة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePassed($query)
    {
        return $query->where('result', MarkResult::PASS);
    }

    /**
     * النتائج الراسبة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('result', MarkResult::FAIL);
    }

    /**
     * النتائج قيد الانتظار
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('result', MarkResult::PENDING);
    }

    /**
     * النتائج المكتملة (ناجح أو راسب)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFinalized($query)
    {
        return $query->whereIn('result', [MarkResult::PASS, MarkResult::FAIL]);
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

    // ────── Methods ──────

    /**
     * حساب العلامة السنوية من الفصلين
     * 
     * @return float|null
     */
    public function calculateYearlyMark(): float|null
    {
        if ($this->term1_mark === null || $this->term2_mark === null) {
            return null;
        }

        return round(($this->term1_mark + $this->term2_mark) / 2, 2);
    }

    /**
     * تحديث النتيجة بناءً على المعدل السنوي
     * 
     * @return MarkResult|null
     */
    public function calculateResult(): MarkResult|null
    {
        if ($this->yearly_mark === null) {
            return MarkResult::PENDING;
        }

        return $this->yearly_mark >= $this->subject->pass_mark 
            ? MarkResult::PASS 
            : MarkResult::FAIL;
    }

    /**
     * التحقق من أن النتيجة ناجحة
     * 
     * @return bool
     */
    public function isPassed(): bool
    {
        return $this->result === MarkResult::PASS;
    }

    /**
     * التحقق من أن النتيجة راسبة
     * 
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->result === MarkResult::FAIL;
    }

    /**
     * التحقق من أن النتيجة قيد الانتظار
     * 
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->result === MarkResult::PENDING;
    }

    // ────── Accessors ──────

    /**
     * العلامة السنوية مع الرمز
     * 
     * @return string
     */
    public function getYearlyMarkDisplayAttribute(): string
    {
        if ($this->yearly_mark === null) {
            return 'لم تُحسب بعد';
        }

        return round($this->yearly_mark, 2) . ' / 100';
    }

   


    /**
     * ملخص النتائج
     * 
     * @return string
     */
    public function getSummaryAttribute(): string
    {
        $term1 = $this->term1_mark ? round($this->term1_mark, 2) : '—';
        $term2 = $this->term2_mark ? round($this->term2_mark, 2) : '—';
        $yearly = $this->yearly_mark ? round($this->yearly_mark, 2) : '—';

        return "{$this->subject->name}: F1={$term1}, F2={$term2}, Yearly={$yearly} ({$this->result_label})";
    }

    /**
     * الأداء الإجمالي (ممتاز، جيد، مقبول، ضعيف)
     * 
     * @return string
     */
    public function getPerformanceLevelAttribute(): string
    {
        if ($this->yearly_mark === null) {
            return 'غير معروف';
        }

        if ($this->yearly_mark >= 90) {
            return 'ممتاز';
        } elseif ($this->yearly_mark >= 80) {
            return 'جيد جداً';
        } elseif ($this->yearly_mark >= 70) {
            return 'جيد';
        } elseif ($this->yearly_mark >= 60) {
            return 'مقبول';
        } else {
            return 'ضعيف';
        }
    }
}
