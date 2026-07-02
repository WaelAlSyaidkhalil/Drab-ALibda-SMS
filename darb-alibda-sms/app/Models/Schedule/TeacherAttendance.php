<?php

namespace App\Models\Schedule;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Academic\Teacher;
use Illuminate\Support\Carbon;

/**
 * نموذج الحضور والغياب
 *
 * @property int $id
 * @property int $teacher_id           FK → teachers
 * @property string $status            حالة الحضور (present, absent, late, excused)
 * @property string|null $reason       سبب الغياب (اختياري)
 * @property Carbon|null $date تاريخ الحصة
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Teacher $teacher
 */
class TeacherAttendance extends Model
{
    use Filterable;

    protected $table = 'teacher_attendance';

    protected $fillable = [
        'teacher_id',
        'status',
        'date',
    ];

    protected $casts = [
        'status' => AttendanceStatus::class,
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────


    /**
     * المعلم
     *
     * @return BelongsTo
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    // ────── Scopes ──────

    /**
     * البحث عن الحضور فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePresent($query)
    {
        return $query->where('status', AttendanceStatus::PRESENT);
    }

    /**
     * البحث عن الغياب
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', AttendanceStatus::ABSENT);
    }

    /**
     * البحث عن التأخر
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLate($query)
    {
        return $query->where('status', AttendanceStatus::LATE);
    }

    /**
     * البحث عن الغياب المعذور
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExcused($query)
    {
        return $query->where('status', 'excused');
    }

    /**
     * البحث حسب التاريخ
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Carbon $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * البحث عن المعلم
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $teacherId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // ────── Methods ──────

    /**
     * التحقق من أن المعلم حاضر
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * التحقق من أن المعلم غائب
     *
     * @return bool
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * التحقق من أن المعلم متأخر
     *
     * @return bool
     */
    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    /**
     * نسبة الحضور (%)
     *
     * @return float
     */
    public function getAttendancePercentage(): float
    {
        $totalTeachers = self::query()->whereHas('teacher', function ($q) {
            $q->whereHas('user', function ($q) {
                $q->where('is_active', true);
            });
        })->count();

        if ($totalTeachers === 0) {
            return 0;
        }

        $presentCount = self::query()->where('date', today())->present()->count();

        return round(($presentCount / $totalTeachers) * 100, 2);
    }
}
