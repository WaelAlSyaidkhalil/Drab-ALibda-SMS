<?php

namespace App\Models\Subjects;

use App\Enums\TermType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasAcademicYear;
use App\Models\Schedule\Schedule;

/**
 * نموذج الفصل الدراسي
 * يمثل الفصلين الدراسيين (الفصل الأول والثاني) في السنة الدراسية
 * 
 * @property int $id
 * @property TermType $type           نوع الفصل (first_term أو second_term)
 * @property string $academic_year   السنة الدراسية (2025-2026)
 * @property \Illuminate\Support\Carbon|null $start_date تاريخ البداية
 * @property \Illuminate\Support\Carbon|null $end_date   تاريخ النهاية
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection $schedules
 * @property-read \Illuminate\Database\Eloquent\Collection $subjects
 */
class Term extends Model
{
    use Filterable, HasAcademicYear;

    protected $fillable = [
        'type',
        'academic_year',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'type' => TermType::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * جميع الحصص في هذا الفصل
     * 
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * المواد المدرسة في هذا الفصل
     * 
     * @return BelongsToMany
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'term_subject',
            'term_id',
            'subject_id'
        );
    }

    // ────── Scopes ──────

    /**
     * الفصل الأول
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFirst($query)
    {
        return $query->where('type', TermType::FIRST_TERM->value);
    }

    /**
     * الفصل الثاني
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSecond($query)
    {
        return $query->where('type', TermType::SECOND_TERM->value);
    }

    /**
     * الفصول النشطة حالياً
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->where('start_date', '<=', $now)
              ->where('end_date', '>=', $now);
        });
    }

    /**
     * الفصول القادمة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    /**
     * الفصول المكتملة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now());
    }

    // ────── Methods ──────

    /**
     * التحقق من أن الفصل نشط حالياً
     * 
     * @return bool
     */
    public function isActive(): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    /**
     * الحصول على عدد الأيام المتبقية
     * 
     * @return int
     */
    public function getDaysRemaining(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    // ────── Accessors ──────

    /**
     * اسم الفصل
     * 
     * @return string
     */
    public function getTermNameAttribute(): string
    {
        return $this->type->label();
    }

    /**
     * عرض السنة الدراسية والفصل معاً
     * 
     * @return string
     */
    public function getAcademicYearAndTermAttribute(): string
    {
        return "{$this->academic_year} - {$this->getTermNameAttribute()}";
    }

    /**
     * حالة الفصل (نشط، قادم، مكتمل)
     * 
     * @return string
     */
    public function getStatusAttribute(): string
    {
        if ($this->isActive()) {
            return 'active';
        }

        if ($this->start_date > now()) {
            return 'upcoming';
        }

        return 'completed';
    }

    /**
     * نص مدة الفصل
     * 
     * @return string
     */
    public function getDurationAttribute(): string
    {
        if (!$this->start_date || !$this->end_date) {
            return 'غير محدد';
        }

        $start = $this->start_date->format('d/m/Y');
        $end = $this->end_date->format('d/m/Y');

        return "{$start} - {$end}";
    }
}
