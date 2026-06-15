<?php

namespace App\Services\Teacher;

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
            'absence_date' => $j->absence_date->format('Y-m-d'),
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
            'created_at' => $j->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * تحديث طلب غياب
     */
    public function updateAbsenceJustification(int $justificationId, array $data, int $reviewerId)
    {
        $updated = $this->repository->updateJustificationStatus($justificationId, [
            'status' => $data['status'],
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by' => $reviewerId,
        ]);

        if (!$updated) {
            throw new \Exception('فشل تحديث طلب الغياب');
        }

        return $this->repository->getJustificationById($justificationId);
    }

    /**
     * حذف طلب غياب
     */
    public function deleteAbsenceJustification(int $justificationId)
    {
        $deleted = $this->repository->deleteJustification($justificationId);

        if (!$deleted) {
            throw new \Exception('فشل حذف طلب الغياب');
        }

        return true;
    }
}
