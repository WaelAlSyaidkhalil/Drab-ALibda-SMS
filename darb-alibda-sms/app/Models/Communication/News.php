<?php

namespace App\Models\Communication;

use App\Enums\AudienceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasAttachments;
use App\Models\Auth\User;
use App\Observers\Admin\NewsObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Carbon;

/**
 * نموذج الأخبار
 *
 * @property int $id
 * @property string $title            عنوان الخبر
 * @property string $body             محتوى الخبر
 * @property string $audience         الجمهور المستهدف (all, teachers, parents, students)
 * @property int $created_by          FK → users (المحرر)
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection $readers
 */
#[ObservedBy(NewsObserver::class)]
class News extends Model
{
    use Filterable, HasAttachments;

    protected $fillable = [
        'title',
        'body',
        'audience',
        'created_by',
    ];

    protected $casts = [
        'audience' => AudienceType::class,
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
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * المستخدمون الذين قرأوا هذا الخبر
     *
     * @return BelongsToMany
     */
    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'news_reads',
            'news_id',
            'user_id'
        )->withTimestamps();
    }

    // ────── Scopes ──────

    /**
     * الأخبار المتاحة للمعلمين فقط
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTeachers($query)
    {
        return $query->whereIn('audience', ['all', 'teachers']);
    }

    /**
     * الأخبار الحديثة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}

