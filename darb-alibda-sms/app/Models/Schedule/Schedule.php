<?php

namespace App\Models\Schedule;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property int $teacher_id           FK → teachers
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
 * @property-read Collection $attendance
 */
class Schedule extends Model
{
    use Filterable, HasStatus;

    protected $fillable = [
        'section_id',
        'subject_id',
        'teacher_id',
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
     * المعلم
     *
     * @return BelongsTo
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
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

    /**
     * الحضور/الغياب
     *
     * @return HasMany
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
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
     * البحث حسب المعلم
     *
     * @param Builder $query
     * @param int $teacherId
     * @return Builder
     */
    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
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
            $q->where('teacher_id', $teacherId)
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
        return Schedule::where('teacher_id', $this->teacher_id)
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

    /**
     * نسبة الحضور (%)
     *
     * @return float
     */
    public function getAttendancePercentage(): float
    {
        $totalStudents = $this->section->enrollments()
            ->where('status', 'active')
            ->count();

        if ($totalStudents === 0) {
            return 0;
        }

        $presentCount = $this->attendance()
            ->where('status', 'present')
            ->count();

        return round(($presentCount / $totalStudents) * 100, 2);
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
