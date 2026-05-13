<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Auth\User;
use Illuminate\Support\Carbon;

/**
 * نموذج الأخبار
 *
 * @property int $id
 * @property int|null $user_id        FK → users (المحرر)
 * @property string $title            عنوان الخبر
 * @property string $content          محتوى الخبر
 * @property string|null $image_url   رابط الصورة
 * @property bool $is_published       هل الخبر منشور
 * @property Carbon|null $published_at تاريخ النشر
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User|null $user
 */
class News extends Model
{
    use Filterable;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_url',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * المحرر
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ────── Scopes ──────

    /**
     * الأخبار المنشورة فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', now());
    }

    /**
     * الأخبار قيد المراجعة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * الأخبار الحديثة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    // ────── Methods ──────

    /**
     * نشر الخبر
     *
     * @return bool
     */
    public function publish(): bool
    {
        return $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * إلغاء نشر الخبر
     *
     * @return bool
     */
    public function unpublish(): bool
    {
        return $this->update(['is_published' => false]);
    }
}
