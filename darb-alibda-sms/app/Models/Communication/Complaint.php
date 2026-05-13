<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Auth\User;

/**
 * نموذج الشكاوى
 * 
 * @property int $id
 * @property int $user_id             FK → users
 * @property string $title            عنوان الشكوى
 * @property string $description      تفاصيل الشكوى
 * @property string $category         فئة الشكوى (عملية تعليمية، موارد...)
 * @property string $status           الحالة (جديدة، قيد المعالجة، مغلقة)
 * @property string|null $response    الرد على الشكوى
 * @property int|null $assigned_to    معرف المسؤول
 * @property \Illuminate\Support\Carbon|null $resolved_at تاريخ الحل
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read User $user
 * @property-read User|null $assignee
 */
class Complaint extends Model
{
    use Filterable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'status',
        'response',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ────── العلاقات ──────

    /**
     * المستخدم الذي أرسل الشكوى
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * المسؤول المعين
     * 
     * @return BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ────── Scopes ──────

    /**
     * الشكاوى الجديدة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * الشكاوى قيد المعالجة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * الشكاوى المغلقة
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
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
}
