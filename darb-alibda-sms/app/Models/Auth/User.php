<?php

namespace App\Models\Auth;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasFullName;
use App\Models\Academic\Student;
use App\Models\Academic\Teacher;
use App\Models\Communication\Conversation;
use App\Models\Communication\Message;
use App\Enums\UserRole;

/**
 * نموذج المستخدم
 * يمثل جميع المستخدمين في النظام (إدارة، معلمين، طلاب، أولياء أمور)
 * 
 * @property int $id
 * @property int $role_id                الدور (FK → roles)
 * @property string $email               بريد إلكتروني فريد
 * @property string|null $phone          رقم الهاتف
 * @property \Illuminate\Support\Carbon $email_verified_at
 * @property string $password            كلمة المرور
 * @property string|null $remember_token
 * @property string|null $first_name     الاسم الأول
 * @property string|null $last_name      الاسم الأخير
 * @property string|null $avatar         صورة الملف الشخصي
 * @property bool|null $is_active        هل المستخدم مفعّل
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read Role $role
 * @property-read Student|null $student
 * @property-read Teacher|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection $children
 * @property-read string $full_name
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Filterable, HasFullName;

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = [
        'role_id',
        'email',
        'phone',
        'password',
        'first_name',
        'last_name',
        'avatar',
        'fcm_token',
        'is_active',
    ];

    /**
     * الحقول المخفية عند التحويل إلى JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل الأنواع
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * دور المستخدم
     * 
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * سجل الطالب (إذا كان المستخدم طالباً)
     * 
     * @return HasOne
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * سجل المعلم (إذا كان المستخدم معلماً)
     * 
     * @return HasOne
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * الأطفال (إذا كان المستخدم ولي أمر)
     * 
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    /**
     * المحادثات التي أرسلها هذا المستخدم
     * 
     * @return HasMany
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    /**
     * الرسائل التي أرسلها هذا المستخدم
     * 
     * @return HasMany
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // ────── Scopes ──────

    /**
     * الأخبار التي قرأها هذا المستخدم
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readNews(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Communication\News::class,
            'news_reads',
            'user_id',
            'news_id'
        )->withTimestamps();
    }

    /**
     * المستخدمون المفعلون فقط
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * المستخدمون المعطلون
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * البحث حسب البريد الإلكتروني
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    /**
     * البحث حسب الدور
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRole($query, string|array $role)
    {
        if (is_array($role)) {
            return $query->whereHas('role', function ($q) use ($role) {
                $q->whereIn('name', $role);
            });
        }

        return $query->whereHas('role', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * المسؤولون والموظفون
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->withRole(['admin', 'staff']);
    }

    /**
     * المعلمون
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTeachers($query)
    {
        return $query->withRole('teacher');
    }

    /**
     * الطلاب
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStudents($query)
    {
        return $query->withRole('student');
    }

    /**
     * أولياء الأمور
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParents($query)
    {
        return $query->withRole('parent');
    }

    // ────── Methods ──────

    /**
     * التحقق من أن المستخدم لديه دور معين
     * 
     * @param string|UserRole $roleName
     * @return bool
     */
    public function hasRole(string|UserRole $roleName): bool
    {
        $name = $roleName instanceof UserRole ? $roleName->value : $roleName;
        return $this->role->name === $name;
    }

    /**
     * التحقق من أن المستخدم مسؤول
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('staff');
    }

    /**
     * التحقق من أن المستخدم معلم
     * 
     * @return bool
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * التحقق من أن المستخدم طالب
     * 
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    /**
     * التحقق من أن المستخدم ولي أمر
     * 
     * @return bool
     */
    public function isParent(): bool
    {
        return $this->hasRole('parent');
    }

    // ────── Accessors ──────

    /**
     * اسم الدور بالعربية
     * 
     * @return string|null
     */
    public function getRoleNameArabicAttribute(): string|null
    {
        return match ($this->role?->name) {
            'admin' => 'مسؤول نظام',
            'teacher' => 'معلم',
            'student' => 'طالب',
            'parent' => 'ولي أمر',
            'staff' => 'موظف',
            default => null,
        };
    }

    /**
     * حالة النشاط بصيغة نصية
     * 
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'مفعّل' : 'معطل';
    }
}
