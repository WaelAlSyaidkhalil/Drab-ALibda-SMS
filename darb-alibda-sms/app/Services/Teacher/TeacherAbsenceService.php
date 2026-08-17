<?php

namespace App\Services\Teacher;

use App\Models\Academic\Student;
use App\Models\Auth\User;
use App\Notifications\Parent\TeacherActionNotification;
use App\Repositories\Teacher\TeacherAbsenceRepository;

class TeacherAbsenceService
{
    public function __construct(protected TeacherAbsenceRepository $repository)
    {
    }

    /**
     * احصل على طلبات الغياب مع تنسيق
     */
    public function getAbsenceJustifications(int $teacherId)
    {
        $justifications = $this->repository->getStudentAbsenceJustifications($teacherId);

        return $justifications->map(fn ($j) => [
            'id' => $j->id,
            'student' => [
                'id' => $j->student->id,
                'name' => $j->student->user->name,
                'email' => $j->student->user->email,
                'phone' => $j->student->user->phone,
                'registry_number' => $j->student->registry_number,
            ],
            'parent' => [
                'id' => $j->parent->id,
                'name' => $j->parent->name,
                'email' => $j->parent->email,
                'phone' => $j->parent->phone,
            ],
            'absence_date' => $j->absence_date?->format('Y-m-d'),
            'reason' => $j->reason,
            'status' => $j->status,
            'review_note' => $j->review_note,
            'reviewed_by' => $j->reviewed_by,
            'reviewed_at' => $j->reviewed_at?->format('Y-m-d H:i:s'),
            'attachments' => $j->attachments->map(fn ($a) => [
                'id' => $a->id,
                'path' => $a->path,
                'file_name' => $a->file_name,
            ]),
            'created_at' => $j->created_at?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * تحديث طلب غياب
     */
    public function updateAbsenceJustification(int $justificationId, array $data, int $reviewerId)
    {
        if (!$this->repository->findJustification($justificationId)) {
            throw new \Exception('طلب الغياب غير موجود', 404);
        }

        $updated = $this->repository->updateJustificationStatus($justificationId, [
            'status' => $data['status'],
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by' => $reviewerId,
        ]);

        if (!$updated) {
            throw new \Exception('فشل تحديث طلب الغياب', 500);
        }

        $justification = $this->repository->getJustificationById($justificationId);
        if ($justification && $justification->student && $justification->student->parent) {
            $teacherUser = User::find($reviewerId);
            $student = $justification->student;
            $statusLabel = match ($data['status'] ?? '') {
                'approved' => 'مقبول',
                'rejected' => 'مرفوض',
                'pending' => 'قيد المراجعة',
                default => 'تم تحديثه',
            };

            $justification->student->parent->notifyNow(new TeacherActionNotification(
                $teacherUser,
                $student,
                'تم تحديث طلب الغياب',
                sprintf('تم تحديث حالة غياب %s إلى %s.', $student->getFullNameAttribute(), $statusLabel),
                ['type' => 'absence_justification']
            ));
        }

        return $justification;
    }

    /**
     * حذف طلب غياب
     */
    public function deleteAbsenceJustification(int $justificationId)
    {
        if (!$this->repository->findJustification($justificationId)) {
            throw new \Exception('طلب الغياب غير موجود', 404);
        }

        $deleted = $this->repository->deleteJustification($justificationId);

        if (!$deleted) {
            throw new \Exception('فشل حذف طلب الغياب', 500);
        }

        return true;
    }
}
