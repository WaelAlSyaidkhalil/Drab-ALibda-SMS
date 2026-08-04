<?php

namespace App\Models\Communication;

use App\Enums\ComplaintStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasAttachments;
use App\Models\Auth\User;

/**
 * نموذج الشكاوى
 * 
 * @property int $id
 * @property int $user_id             FK → users
 * @property string $title            عنوان الشكوى
 * @property string $body             تفاصيل الشكوى
 * @property string $status           الحالة (جديدة، قيد المعالجة، مغلقة)
 * @property string|null $response    الرد على الشكوى
 * @property \Illuminate\Support\Carbon|null $resolved_at تاريخ الحل
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read User $user
 * @property-read User|null $assignee
 */
class Complaint extends Model
{
    use Filterable, HasAttachments;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'status',
        'response',
        'resolved_at',
    ];

    protected $casts = [
        'status' => ComplaintStatus::class,
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


    // ────── Scopes ──────

  public function scopePending($query)
{
    return $query->where('status', ComplaintStatus::PENDING->value);
}

public function scopeInProgress($query)
{
    return $query->where('status', ComplaintStatus::IN_PROGRESS->value);
}

public function scopeResolved($query)
{
    return $query->where('status', ComplaintStatus::RESOLVED->value);
}

}
