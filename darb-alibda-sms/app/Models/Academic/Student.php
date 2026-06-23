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
use Illuminate\Support\Carbon;

/**
 * نموذج الطالب
 * يمثل معلومات الطالب الأساسية
 * (العلاقات الأكاديمية تكون مع StudentEnrollment)
 *
 * @property int $id
 * @property int $user_id              FK → users
 * @property int|null $parent_id       FK → users (ولي الأمر)
 * @property string $first_name        الاسم الأول
 * @property string $last_name         الاسم الأخير
 * @property string|null $father_name  اسم الأب
 * @property string|null $mother_name  اسم الأم
 * @property string|null $national_id  الرقم الوطني
 * @property string|null $registry_number رقم التسجيل
 * @property Carbon|null $birth_date تاريخ الميلاد
 * @property string $gender            الجنس (male, female)
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user
 * @property-read User|null $parent
 * @property-read Collection $enrollments
 * @property-read StudentEnrollment|null $currentEnrollment
 * @property-read string $full_name
 * @property-read int $age
 */
class Student extends Model
{
    use Filterable, HasFullName;

    protected $fillable = [
        'user_id',
        'parent_id',
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'national_id',
        'registry_number',
        'birth_date',
        'gender',
    ];

    protected $casts = [
        'birth_date' => 'date',
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
     * ولي الأمر (إن وجد)
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * جميع التسجيلات الأكاديمية للطالب عبر السنوات
     *
     * @return HasMany
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    // ────── Scopes ──────

    /**
     * البحث حسب رقم التسجيل
     *
     * @param Builder $query
     * @param string $registryNumber
     * @return Builder
     */
    public function scopeByRegistry($query, string $registryNumber)
    {
        return $query->where('registry_number', $registryNumber);
    }

    /**
     * البحث حسب الرقم الوطني
     *
     * @param Builder $query
     * @param string $nationalId
     * @return Builder
     */
    public function scopeByNationalId($query, string $nationalId)
    {
        return $query->where('national_id', $nationalId);
    }

    /**
     * البحث حسب الجنس
     *
     * @param Builder $query
     * @param string $gender (male|female)
     * @return Builder
     */
    public function scopeByGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * البحث حسب ولي الأمر
     *
     * @param Builder $query
     * @param int $parentId
     * @return Builder
     */
    public function scopeByParent($query, int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    // ────── Methods ──────

    /**
     * الحصول على التسجيل النشط الحالي
     *
     * @return StudentEnrollment|null
     */
    public function getCurrentEnrollment(): StudentEnrollment|null
    {
        return $this->enrollments()
            ->where('status', 'active')
            ->latest('created_at')
            ->first();
    }

    /**
     * الحصول على الشعبة الحالية
     *
     * @return Section|null
     */
    public function getCurrentSection(): Section|null
    {
        return $this->getCurrentEnrollment()?->section;
    }

    /**
     * الحصول على الصف الحالي
     *
     * @return SchoolClass|null
     */
    public function getCurrentClass(): SchoolClass|null
    {
        return $this->getCurrentSection()?->schoolClass;
    }

    // ────── Accessors ──────

    /**
     * العمر الحالي
     *
     * @return int|null
     */
    public function getAgeAttribute(): int|null
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->diffInYears(now());
    }

    /**
     * الاسم الكامل (الأول + الأخير)
     * مع دعم من الـ HasFullName trait
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        $parts = [];

        if (!empty($this->first_name)) {
            $parts[] = $this->first_name;
        }

        if (!empty($this->father_name)) {
            $parts[] = $this->father_name;
        }
        
        if (!empty($this->last_name)) {
            $parts[] = $this->last_name;
        }

        return implode(' ', $parts) ?: 'بدون اسم';
    }
}
