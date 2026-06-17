<?php

namespace App\Models\Schedule;

use App\Enums\TimeSlotNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Filterable;
use App\Models\Schedule\Schedule;
use Illuminate\Support\Carbon;

/**
 * نموذج فترة زمنية (الحصة)
 * يمثل أوقات الحصص الثابتة على مستوى المدرسة
 *
 * @property int $id
 * @property TimeSlotNumber $period_number        رقم الحصة (1، 2، 3...)
 * @property Carbon $start_time وقت البداية
 * @property Carbon $end_time   وقت النهاية
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection $schedules
 */
class TimeSlot extends Model
{
    use Filterable;

    protected $fillable = [
        'period_number',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'period_number' => TimeSlotNumber::class,
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * جميع الجدول الدراسي المرتبطة بهذه الفترة الزمنية
     *
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // ────── Scopes ──────

    /**
     * الترتيب حسب رقم الحصة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('period_number');
    }

    /**
     * البحث عن حصة في وقت معين
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $time الوقت (HH:MM)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAtTime($query, string $time)
    {
        return $query->whereTime('start_time', '<=', $time)
                    ->whereTime('end_time', '>', $time);
    }

    // ────── Methods ──────

    /**
     * التحقق من تضارب الأوقات مع حصة أخرى
     *
     * @param TimeSlot $other
     * @return bool
     */
    public function conflictsWith(TimeSlot $other): bool
    {
        return $this->start_time < $other->end_time &&
               $this->end_time > $other->start_time;
    }

    /**
     * المدة بالدقائق
     *
     * @return int
     */
    public function getDurationInMinutes(): int
    {
        return (int) $this->start_time->diffInMinutes($this->end_time);
    }

    // ────── Accessors ──────

    /**
     * عرض الحصة (الوقت)
     *
     * @return string مثلاً "08:00 - 08:45"
     */
    public function getDisplayTimeAttribute(): string
    {
        $start = $this->start_time->format('H:i');
        $end = $this->end_time->format('H:i');
        return "{$start} - {$end}";
    }

    /**
     * اسم الحصة
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->period_number->label();
    }

    /**
     * المدة بصيغة نصية
     *
     * @return string
     */
    public function getDurationDisplayAttribute(): string
    {
        $minutes = $this->getDurationInMinutes();

        if ($minutes < 60) {
            return "{$minutes} دقيقة";
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return "{$hours}h {$mins}m";
    }

    
}
