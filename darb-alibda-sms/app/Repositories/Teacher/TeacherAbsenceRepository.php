<?php

namespace App\Repositories\Teacher;

use App\Models\Communication\AbsenceJustification;
use App\Models\Communication\News;
use App\Models\Schedule\Schedule;
use Carbon\Carbon;

class TeacherAbsenceRepository
{
    /**
     * احصل على طلبات الغياب الخاصة بطلاب المعلم
     */
    public function getStudentAbsenceJustifications(int $teacherId)
    {
        $sectionIds = Schedule::where('teacher_id', $teacherId)
            ->pluck('section_id')
            ->unique();

        return AbsenceJustification::query()
            ->whereHas('student', fn ($query) =>
                $query->whereHas('enrollments', fn ($q) =>
                    $q->whereIn('section_id', $sectionIds)
                        ->where('status', 'active')
                )
            )
            ->with([
                'student' => fn ($q) => $q->with('user'),
                'parent' => fn ($q) => $q->select('id', 'name', 'email', 'phone'),
                'attachments',
            ])
            ->latest('created_at')
            ->get();
    }

    /**
     * حصول على طلب غياب محدد
     */
    public function getJustificationById(int $justificationId)
    {
        return AbsenceJustification::with([
            'student' => fn ($q) => $q->with('user'),
            'parent',
            'reviewer',
            'attachments',
        ])->find($justificationId);
    }

    /**
     * تحديث حالة طلب الغياب
     */
    public function updateJustificationStatus(int $justificationId, array $data)
    {
        $justification = AbsenceJustification::find($justificationId);

        if (!$justification) {
            return null;
        }

        return $justification->update([
            'status' => $data['status'],
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by' => $data['reviewed_by'],
            'reviewed_at' => now(),
        ]);
    }

    /**
     * حذف طلب غياب مع مرفقاته
     */
    public function deleteJustification(int $justificationId)
    {
        $justification = AbsenceJustification::find($justificationId);

        if (!$justification) {
            return false;
        }

        $justification->attachments()->delete();

        return $justification->delete();
    }
}
