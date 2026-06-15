<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasAcademicYear;
use App\Models\Traits\HasStatus;
use App\Models\Grading\StudentMark;
use App\Models\Grading\StudentSubjectResult;
use App\Enums\StudentStatus;
use Illuminate\Support\Carbon;

/**
 * نموذج تسجيل الطالب (Enrollment)
 * يمثل تسجيل الطالب في شعبة معينة خلال سنة دراسية محددة
 *
 * @property int $id
 * @property int $student_id           FK → students
 * @property int $section_id           FK → sections
 * @property string $academic_year     السنة الدراسية (2025-2026)
 * @property Carbon $enrollment_date تاريخ التسجيل
 * @property string $status            حالة التسجيل (active, promoted, repeated...)
 * @property string $final_result      النتيجة النهائية (pass, fail, pending)
 * @property float|null $final_average المعدل النهائي
 * @property string|null $notes        ملاحظات إدارية
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Student $student
 * @property-read Section $section
 * @property-read Collection $marks
 * @property-read Collection $subjectResults
 */
class StudentEnrollment extends Model
{
    use Filterable, HasAcademicYear, HasStatus;

    protected $fillable = [
        'student_id',
        'section_id',
        'academic_year',
        'enrollment_date',
        'status',
        'final_result',
        'final_average',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'final_average' => 'float',
        'status' => StudentStatus::class,
        'final_result' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * الطالب المسجل
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * الشعبة المسجل فيها
     *
     * @return BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * جميع علامات الطالب في هذا التسجيل
     *
     * @return HasMany
     */
    public function marks(): HasMany
    {
        return $this->hasMany(StudentMark::class);
    }

    /**
     * نتائج الطالب في المواد
     *
     * @return HasMany
     */
    public function subjectResults(): HasMany
    {
        return $this->hasMany(StudentSubjectResult::class);
    }

    // ────── Scopes ──────

    /**
     * التسجيلات النشطة فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', StudentStatus::ACTIVE);
    }

    /**
     * التسجيلات المكتملة (ناجح أو راسب)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('final_result', ['pass', 'fail']);
    }

    /**
     * البحث حسب الصف الدراسي
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $classId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInClass($query, int $classId)
    {
        return $query->whereHas('section', function ($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }

    /**
     * البحث حسب الشعبة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $sectionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInSection($query, int $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * الطلاب الناجحون
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePassed($query)
    {
        return $query->where('final_result', 'pass');
    }

    /**
     * الطلاب الراسبون
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('final_result', 'fail');
    }

    // ────── Methods ──────

    /**
     * الحصول على الصف
     *
     * @return SchoolClass|null
     */
    public function getClass(): SchoolClass|null
    {
        return $this->section?->schoolClass;
    }

    /**
     * التحقق من كون التسجيل نشطاً
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === StudentStatus::ACTIVE;
    }

    /**
     * التحقق من كون النتيجة نهائية
     *
     * @return bool
     */
    public function isResultFinalized(): bool
    {
        return $this->final_result !== 'pending';
    }


    /**
     * نص النتيجة النهائية بالعربية
     *
     * @return string
     */
    public function getResultLabelAttribute(): string
    {
        return match ($this->final_result) {
            'pass' => 'ناجح ✅',
            'fail' => 'راسب ❌',
            'pending' => 'قيد الانتظار ⏳',
            default => 'غير معروف',
        };
    }

    /**
     * عدد المواد المسجل فيها
     *
     * @return int
     */
    public function getSubjectCountAttribute(): int
    {
        return $this->subjectResults()->count();
    }

    /**
     * المعدل النهائي مع الرمز
     *
     * @return string
     */
    public function getFinalAverageDisplayAttribute(): string
    {
        if ($this->final_average === null) {
            return 'لم يُحسب بعد';
        }

        return number_format($this->final_average, 2) . ' / 100';
    }
}
