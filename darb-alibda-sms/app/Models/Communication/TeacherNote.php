<?php

namespace App\Models\Communication;

use App\Models\Academic\Student;
use App\Models\Auth\User;
use App\Models\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherNote extends Model
{
    use HasAttachments;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'title',
        'content',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scopeForParent($query, int $parentId)
    {
        return $query->whereHas('student', function ($studentQuery) use ($parentId) {
            $studentQuery->where('parent_id', $parentId);
        });
    }

    public function isRead(): bool
    {
        return (bool) $this->is_read;
    }
}
