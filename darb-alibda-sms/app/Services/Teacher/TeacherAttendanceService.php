<?php

namespace App\Services\Teacher;

use App\Repositories\Teacher\TeacherAttendanceRepository;
use App\Models\Academic\Section;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeacherAttendanceService
{
    public function __construct(protected TeacherAttendanceRepository $repository)
    {
    }

    public function getSectionsWithStudents(int $teacherId, ?int $classId, ?int $sectionId, ?string $date): Collection
    {
        return $this->repository->getTeacherSectionsWithStudents($teacherId, $classId, $sectionId, $date);
    }

    public function updateSectionAttendance(int $teacherId, int $sectionId, string $date, array $updates, ?int $scheduleId = null): array
    {
        $scheduleIds = $this->repository->getTeacherSectionScheduleIds($teacherId, $sectionId, $scheduleId);

        if ($scheduleIds->isEmpty()) {
            throw new \Exception('لا يوجد جدول صالح لهذا المعلم في هذه الشعبة أو معرّف الحصة غير صحيح');
        }

        $activeStudentIds = $this->repository->getActiveStudentIdsForSection($sectionId);

        if ($activeStudentIds->isEmpty()) {
            throw new \Exception('لا يوجد طلاب نشطون في هذه الشعبة');
        }

        $statuses = [];
        $sentStudentIds = collect($updates)->pluck('student_id')->unique();
        $invalidStudentIds = $sentStudentIds->diff($activeStudentIds);

        if ($invalidStudentIds->isNotEmpty()) {
            throw new \Exception('بعض الطلاب غير موجودين في هذه الشعبة: ' . $invalidStudentIds->join(', '));
        }

        foreach ($activeStudentIds as $studentId) {
            $matching = collect($updates)->firstWhere('student_id', $studentId);
            $statuses[$studentId] = [
                'status' => $matching['status'] ?? 'present',
            ];
        }

        $counts = $this->repository->syncSectionAttendance(
            $sectionId,
            Carbon::parse($date)->toDateString(),
            collect($statuses),
            $scheduleIds
        );

        return [
            'section_id' => $sectionId,
            'date' => Carbon::parse($date)->toDateString(),
            'counts' => $counts,
        ];
    }
}
