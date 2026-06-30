<?php

namespace App\Models\Schedule;

use App\Enums\AttendanceStatus;
use App\Models\Academic\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Academic\Student;
use Illuminate\Support\Carbon;

/**
 * نموذج الحضور والغياب
 *
 * @property int $id
 * @property int $section_id          FK → sections
 * @property int $student_id           FK → students
 * @property string $status            حالة الحضور (present, absent, late, excused)
 * @property string|null $reason       سبب الغياب (اختياري)
 * @property Carbon|null $date تاريخ الحصة
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Section $section
 * @property-read Student $student
 */
class Attendance extends Model
{
    use Filterable;

    protected $table = 'attendance';

    protected $fillable = [
        'section_id',
        'student_id',
        'status',
        'reason',
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
     * الحصة
     *
     * @return BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * الطالب
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
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
        return $query->where('status', 'present');
    }

    /**
     * البحث عن الغياب
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * البحث عن التأخر
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
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
     * البحث عن الطالب
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // ────── Methods ──────

    /**
     * التحقق من أن الطالب حاضر
     *
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }

    /**
     * التحقق من أن الطالب غائب
     *
     * @return bool
     */
    public function isAbsent(): bool
    {
        return $this->status === 'absent';
    }

    /**
     * التحقق من أن الطالب متأخر
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
        $totalStudents = $this->section->enrollments->active()->count();

        if ($totalStudents === 0) {
            return 0;
        }

        $presentCount = $this->present()->count();

        return round(($presentCount / $totalStudents) * 100, 2);
    }


    // ────── Accessors ──────

    /**
     * حالة الحضور بالعربية
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present' => 'حاضر ✅',
            'absent' => 'غائب ❌',
            'late' => 'متأخر ⏱️',
            'excused' => 'معذور 📝',
            default => 'غير معروف',
        };
    }

    /**
     * الأيقونة المناسبة للحالة
     *
     * @return string
     */
    public function getIconAttribute(): string
    {
        return match ($this->status) {
            'present' => '✅',
            'absent' => '❌',
            'late' => '⏱️',
            'excused' => '📝',
            default => '❓',
        };
    }
}
