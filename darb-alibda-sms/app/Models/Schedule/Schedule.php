<?php

namespace App\Models\Schedule;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasStatus;
use App\Models\Academic\Section;
use App\Models\Academic\Teacher;
use App\Models\Subjects\Subject;
use App\Models\Subjects\Term;
use Illuminate\Support\Carbon;

/**
 * نموذج الجدول الدراسي
 * يمثل الحصة المخصصة (معلم + مادة + شعبة + وقت + يوم)
 *
 * @property int $id
 * @property int $section_id           FK → sections
 * @property int $subject_id           FK → subjects
 * @property int $term_id              FK → terms
 * @property int $time_slot_id         FK → time_slots
 * @property DayOfWeek $day            يوم الأسبوع (sun, mon, tue...)
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Section $section
 * @property-read Subject $subject
 * @property-read Teacher $teacher
 * @property-read Term $term
 * @property-read TimeSlot $timeSlot
 */
class Schedule extends Model
{
    use Filterable, HasStatus;

    protected $fillable = [
        'section_id',
        'subject_id',
        'term_id',
        'time_slot_id',
        'day',
    ];

    protected $casts = [
        'day' => DayOfWeek::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * الشعبة
     *
     * @return BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
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
     * المعلم المسؤول عن المادة
     *
     * @return HasOneThrough
     */
    public function teacher(): HasOneThrough
    {
        return $this->hasOneThrough(
            Teacher::class,
            Subject::class,
            'teacher_id', // subject.teacher_id
            'id', // teacher.id
            'subject_id', // schedule.subject_id
            'id' // subject.id
        );
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

    /**
     * الفترة الزمنية (الحصة)
     *
     * @return BelongsTo
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    // ────── Scopes ──────

    /**
     * البحث حسب الشعبة
     *
     * @param Builder $query
     * @param int $sectionId
     * @return Builder
     */
    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * البحث حسب اليوم
     *
     * @param Builder $query
     * @param DayOfWeek|string $day
     * @return Builder
     */
    public function scopeForDay($query, DayOfWeek|string $day)
    {
        $dayValue = $day instanceof DayOfWeek ? $day->value : $day;
        return $query->where('day', $dayValue);
    }


    /**
     * البحث حسب الفصل الدراسي
     *
     * @param Builder $query
     * @param int $termId
     * @return Builder
     */
    public function scopeForTerm($query, int $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * الحصص حسب المعلم
     *
     * @param Builder $query
     * @param int $teacherId
     * @return Builder
     */
    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->whereHas('subject', fn ($q) => $q->where('teacher_id', $teacherId));
    }

    /**
     * الحصص المتاحة لتعيين معلم (بدون تضارب)
     *
     * @param Builder $query
     * @param int $teacherId
     * @param DayOfWeek|string $day
     * @param int $timeSlotId
     * @return Builder
     */
    public function scopeAvailableForTeacher($query, int $teacherId, DayOfWeek|string $day, int $timeSlotId)
    {
        $dayValue = $day instanceof DayOfWeek ? $day->value : $day;

        return $query->whereNot(function ($q) use ($teacherId, $dayValue, $timeSlotId) {
            $q->whereHas('subject', fn ($sq) => $sq->where('teacher_id', $teacherId))
              ->where('day', $dayValue)
              ->where('time_slot_id', $timeSlotId);
        });
    }

    // ────── Methods ──────
    /**
     * التحقق من وجود تضارب زمني للمعلم
     *
     * @return bool
     */
    public function hasTeacherConflict(): bool
    {
        $teacherId = $this->subject?->teacher_id
            ?? Subject::where('id', $this->subject_id)->value('teacher_id');

        if (! $teacherId) {
            return false;
        }

        return Schedule::whereHas('subject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->where('day', $this->day)
            ->where('time_slot_id', $this->time_slot_id)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    /**
     * التحقق من وجود تضارب زمني للشعبة
     *
     * @return bool
     */
    public function hasSectionConflict(): bool
    {
        return Schedule::where('section_id', $this->section_id)
            ->where('day', $this->day)
            ->where('time_slot_id', $this->time_slot_id)
            ->where('id', '!=', $this->id)
            ->exists();
    }


    // ────── Accessors ──────

    /**
     * عرض كامل للحصة
     *
     * @return string
     */
    public function getDisplayAttribute(): string
    {
        $dayLabel = ($this->day instanceof DayOfWeek) ? $this->day->label() : $this->day;
        return "{$this->section->full_name} - {$this->subject->name} - {$dayLabel} - {$this->timeSlot->display_time}";
    }

    /**
     * التحقق من وجود تضارب
     *
     * @return bool
     */
    public function hasConflictAttribute(): bool
    {
        return $this->hasTeacherConflict() || $this->hasSectionConflict();
    }

    /**
     * حالة التضارب (نص)
     *
     * @return string
     */
    public function getConflictStatusAttribute(): string
    {
        if ($this->hasTeacherConflict()) {
            return 'المعلم لديه حصة أخرى بنفس الوقت';
        }

        if ($this->hasSectionConflict()) {
            return 'الشعبة لديها حصة أخرى بنفس الوقت';
        }

        return 'بدون تضارب';
    }
}
