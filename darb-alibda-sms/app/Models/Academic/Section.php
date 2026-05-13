<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasAcademicYear;
use App\Models\Schedule\Schedule;
use Illuminate\Support\Carbon;

/**
 * نموذج الشعبة الدراسية
 * تمثل مجموعة من الطلاب في نفس الصف ونفس السنة الدراسية
 *
 * @property int $id
 * @property int $school_class_id      FK → classes
 * @property string $name              اسم الشعبة (أ، ب، ج...)
 * @property int $capacity             السعة القصوى للطلاب
 * @property string $academic_year     السنة الدراسية (2025-2026)
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read SchoolClass $schoolClass
 * @property-read Collection $enrollments
 * @property-read Collection $schedules
 * @property-read string $full_name
 */
class Section extends Model
{
    use Filterable, HasAcademicYear;

    protected $fillable = [
        'school_class_id',
        'name',
        'capacity',
        'academic_year',
    ];

    protected $casts = [
        'capacity' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * الصف الذي تتبع له هذه الشعبة
     *
     * @return BelongsTo
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * جميع تسجيلات الطلاب في هذه الشعبة
     *
     * @return HasMany
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * جميع الحصص الموزعة على هذه الشعبة
     *
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // ────── Scopes ──────

    /**
     * البحث حسب اسم الشعبة
     *
     * @param Builder $query
     * @param string $name
     * @return Builder
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * الشعب غير الممتلئة (عدد الطلاب < السعة)
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithAvailableCapacity($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM student_enrollments WHERE student_enrollments.section_id = sections.id AND status = "active") < capacity');
    }

    /**
     * الشعب الممتلئة
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFull($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM student_enrollments WHERE student_enrollments.section_id = sections.id AND status = "active") >= capacity');
    }

    // ────── Methods ──────

    /**
     * التحقق من وجود مقاعد متاحة
     *
     * @return bool
     */
    public function hasAvailableCapacity(): bool
    {
        $enrolledCount = $this->enrollments()
            ->where('status', 'active')
            ->count();

        return $enrolledCount < $this->capacity;
    }

    /**
     * عدد المقاعد المتبقية
     *
     * @return int
     */
    public function getRemainingCapacity(): int
    {
        $enrolledCount = $this->enrollments()
            ->where('status', 'active')
            ->count();

        return max(0, $this->capacity - $enrolledCount);
    }

    // ────── Accessors ──────

    /**
     * الاسم الكامل للشعبة (مثلاً: الثاني الإعدادي - ج)
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->schoolClass->name} - {$this->name}";
    }

    /**
     * عدد الطلاب المسجلين حالياً
     *
     * @return int
     */
    public function getStudentCountAttribute(): int
    {
        return $this->enrollments()
            ->where('status', 'active')
            ->count();
    }

    /**
     * نسبة الامتلاء (%)
     *
     * @return float
     */
    public function getCapacityPercentageAttribute(): float
    {
        if ($this->capacity === 0) {
            return 0;
        }

        return round(($this->student_count / $this->capacity) * 100, 2);
    }
}
