<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\Filterable;
use App\Models\Auth\User;

/**
 * نموذج المحادثة
 * تمثل محادثة بين مستخدمين
 * 
 * @property int $id
 * @property int $user1_id            FK → users (المستخدم الأول)
 * @property int $user2_id            FK → users (المستخدم الثاني)
 * @property string|null $subject     موضوع المحادثة
 * @property \Illuminate\Support\Carbon|null $last_message_at آخر رسالة
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read User $user1
 * @property-read User $user2
 * @property-read \Illuminate\Database\Eloquent\Collection $messages
 */
class Conversation extends Model
{
    use Filterable;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'subject',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * المستخدم الأول
     * 
     * @return BelongsTo
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المستخدم الثاني
     * 
     * @return BelongsTo
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * جميع الرسائل في هذه المحادثة
     * 
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // ────── Scopes ──────

    /**
     * المحادثات النشطة (بها رسائل)
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereHas('messages');
    }

    /**
     * البحث حسب المستخدم
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user1_id', $userId)
              ->orWhere('user2_id', $userId);
        });
    }

    // ────── Methods ──────

    /**
     * الحصول على المستخدم الآخر
     * 
     * @param int $currentUserId
     * @return User|null
     */
    public function getOtherUser(int $currentUserId): User|null
    {
        return $this->user1_id === $currentUserId ? $this->user2 : $this->user1;
    }

    /**
     * عدد الرسائل
     * 
     * @return int
     */
    public function getMessageCount(): int
    {
        return $this->messages()->count();
    }

    /**
     * آخر رسالة
     * 
     * @return Message|null
     */
    public function getLastMessage(): Message|null
    {
        return $this->messages()->latest()->first();
    }

    // ────── Accessors ──────

    /**
     * اسم المحادثة
     * 
     * @return string
     */
    public function getTitleAttribute(): string
    {
        return $this->subject ?? 'محادثة';
    }
}
