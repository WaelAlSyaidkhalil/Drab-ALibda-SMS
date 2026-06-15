<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Auth\User;

/**
 * نموذج الرسالة
 * تمثل رسالة في محادثة
 * 
 * @property int $id
 * @property int $conversation_id      FK → conversations
 * @property int $sender_id            FK → users
 * @property string $message           نص الرسالة
 * @property bool $is_read             هل تم قراءة الرسالة
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read Conversation $conversation
 * @property-read User $sender
 */
class Message extends Model
{
    use Filterable;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * المحادثة
     * 
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * المرسل
     * 
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ────── Scopes ──────

    /**
     * الرسائل المقروءة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * الرسائل غير المقروءة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * البحث حسب المرسل
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $senderId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromSender($query, int $senderId)
    {
        return $query->where('sender_id', $senderId);
    }

    // ────── Methods ──────

    /**
     * تحديد الرسالة كمقروءة
     * 
     * @return bool
     */
    public function markAsRead(): bool
    {
        if ($this->is_read) {
            return false;
        }

        return $this->update(['is_read' => true]);
    }

    /**
     * التحقق من كون الرسالة مقروءة
     * 
     * @return bool
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    // ────── Accessors ──────

    /**
     * الوقت المنسوب
     * 
     * @return string
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * حالة القراءة
     * 
     * @return string
     */
    public function getStatusAttribute(): string
    {
        return $this->isRead() ? 'مقروءة' : 'لم تُقرأ';
    }
}
