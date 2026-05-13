<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Auth\User;
use Illuminate\Support\Carbon;

/**
 * نموذج الاقتراحات
 *
 * @property int $id
 * @property int $user_id             FK → users
 * @property string $title            عنوان الاقتراح
 * @property string $description      تفاصيل الاقتراح
 * @property string $category         فئة الاقتراح
 * @property bool $is_acknowledged    هل تم إقرار الاقتراح
 * @property string|null $feedback    التعليق على الاقتراح
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user
 */
class Suggestion extends Model
{
    use Filterable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'is_acknowledged',
        'feedback',
    ];

    protected $casts = [
        'is_acknowledged' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * المستخدم
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ────── Scopes ──────

    /**
     * الاقتراحات غير المُقرّة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('is_acknowledged', false);
    }

    /**
     * الاقتراحات المُقرّة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAcknowledged($query)
    {
        return $query->where('is_acknowledged', true);
    }

    /**
     * البحث حسب الفئة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ────── Methods ──────

    /**
     * إقرار الاقتراح
     *
     * @param string $feedback
     * @return bool
     */
    public function acknowledge(string $feedback = ''): bool
    {
        return $this->update([
            'is_acknowledged' => true,
            'feedback' => $feedback ?: $this->feedback,
        ]);
    }
}
