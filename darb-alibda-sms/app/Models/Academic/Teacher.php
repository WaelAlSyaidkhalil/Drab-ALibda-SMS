<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasFullName;
use App\Models\Auth\User;
use App\Models\Schedule\Schedule;
use Illuminate\Support\Carbon;

/**
 * نموذج المعلم
 *
 * @property int $id
 * @property int $user_id              FK → users
 * @property string $first_name        الاسم الأول
 * @property string $last_name         الاسم الأخير
 * @property string|null $specialization التخصص (رياضيات، عربي...)
 * @property string|null $national_id  الرقم الوطني
 * @property string|null $registry_number رقم التسجيل
 * @property string|null $phone        رقم الهاتف
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user
 * @property-read Collection $schedules
 * @property-read string $full_name
 */
class Teacher extends Model
{
    use Filterable, HasFullName;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'national_id',
        'registry_number',
        'specialization',
        'employee_number',
        'hire_date',
        'employment_type',
        'grade',
        'address',
        'phone_alt',
        'experience_years',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'experience_years' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * حساب المستخدم المرتبط
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * جميع الحصص الموزعة على هذا المعلم
     *
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    // ────── Scopes ──────

    /**
     * البحث حسب التخصص
     *
     * @param Builder $query
     * @param string $specialization
     * @return Builder
     */
    public function scopeBySpecialization($query, string $specialization)
    {
        return $query->where('specialization', 'like', "%{$specialization}%");
    }

    /**
     * المعلمون النشطون (المستخدم مفعّل)
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });
    }

    // ────── Methods ──────

    /**
     * الحصول على عدد الحصص في هذا الفصل
     *
     * @param int $termId
     * @return int
     */
    public function getScheduleCountForTerm(int $termId): int
    {
        return $this->schedules()
            ->where('term_id', $termId)
            ->count();
    }

    // ────── Accessors ──────

    /**
     * الاسم الكامل (الأول + الأوسط + الأخير)
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        $parts = [];

        if (!empty($this->first_name)) {
            $parts[] = $this->first_name;
        }

        if (!empty($this->last_name)) {
            $parts[] = $this->last_name;
        }

        return implode(' ', $parts) ?: 'بدون اسم';
    }

    /**
     * اسم التخصص مع اختصار
     *
     * @return string
     */
    public function getSpecializationShortAttribute(): string
    {
        return match (strtolower($this->specialization ?? '')) {
            'mathematics' => 'رياضيات',
            'arabic' => 'لغة عربية',
            'english' => 'لغة إنجليزية',
            'science' => 'علوم',
            'history' => 'تاريخ',
            'geography' => 'جغرافيا',
            'physics' => 'فيزياء',
            'chemistry' => 'كيمياء',
            'biology' => 'أحياء',
            default => $this->specialization ?? 'غير محدد',
        };
    }
}
