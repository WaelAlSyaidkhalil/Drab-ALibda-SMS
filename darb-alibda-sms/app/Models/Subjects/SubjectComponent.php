<?php

namespace App\Models\Subjects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Filterable;
use App\Models\Grading\StudentMark;
use App\Models\Subjects\Subject;

/**
 * نموذج مكون المادة
 * يمثل أنواع التقييم للمادة (كتابي، شفهي، وظائف... إلخ)
 * 
 * @property int $id
 * @property int $subject_id           FK → subjects
 * @property string $name              اسم المكون (كتابي، شفهي، وظائف)
 * @property float $out_of             الدرجة العليا (مثلاً 20)
 * @property float $weight             وزن المكون في الحساب (مثلاً 0.5 = 50%)
 * @property int $order                ترتيب المكون
 * @property string|null $description  وصف
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read Subject $subject
 * @property-read \Illuminate\Database\Eloquent\Collection $marks
 */
class SubjectComponent extends Model
{
    use Filterable;

    protected $fillable = [
        'subject_id',
        'name',
        'out_of',
        'weight',
        'order',
        'description',
    ];

    protected $casts = [
        'out_of' => 'float',
        'weight' => 'float',
        'order' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * المادة الأب
     * 
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * جميع علامات الطلاب في هذا المكون
     * 
     * @return HasMany
     */
    public function marks(): HasMany
    {
        return $this->hasMany(StudentMark::class);
    }

    // ────── Scopes ──────

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
     * الترتيب حسب الأولوية
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // ────── Methods ──────

    /**
     * التحقق من صحة الوزن
     * 
     * @return bool
     */
    public function isValidWeight(): bool
    {
        return $this->weight >= 0 && $this->weight <= 1;
    }

    /**
     * حساب النسبة المئوية للمكون
     * 
     * @return float
     */
    public function getPercentage(): float
    {
        return $this->weight * 100;
    }

    // ────── Accessors ──────

    /**
     * عرض الوزن كنسبة مئوية
     * 
     * @return string
     */
    public function getWeightPercentageAttribute(): string
    {
        return round($this->weight * 100) . '%';
    }

    /**
     * عرض الدرجة الكاملة
     * 
     * @return string
     */
    public function getOutOfDisplayAttribute(): string
    {
        return "من {$this->out_of}";
    }

    /**
     * تفاصيل المكون
     * 
     * @return string
     */
    public function getDetailAttribute(): string
    {
        return "{$this->name} ({$this->weight_percentage}) - {$this->out_of_display}";
    }
}
