<?php

namespace App\Repositories\Teacher;

use App\Models\Communication\TeacherNote;
use App\Models\Academic\StudentEnrollment;
use App\Models\Schedule\Schedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class TeacherNoteRepository
{
    public function getTeacherSectionIds(int $teacherId): SupportCollection
    {
        return Schedule::query()
            ->whereHas('subject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->pluck('section_id')
            ->unique()
            ->values();
    }

    public function getActiveStudentIdsForSections(array $sectionIds): SupportCollection
    {
        return StudentEnrollment::query()
            ->active()
            ->whereIn('section_id', $sectionIds)
            ->pluck('student_id')
            ->unique()
            ->values();
    }

    public function createTeacherNotes(int $teacherUserId, array $studentIds, string $title, string $content): Collection
    {
        $ids = [];
        foreach ($studentIds as $studentId) {
            $created = TeacherNote::create([
                'teacher_id' => $teacherUserId,
                'student_id' => $studentId,
                'title' => $title,
                'content' => $content,
                'is_read' => false,
                'read_at' => null,
            ]);

            $ids[] = $created->id;
        }

        return TeacherNote::query()->whereIn('id', $ids)->get();
    }

    public function getTeacherNotes(int $teacherUserId)
    {
        return TeacherNote::query()
            ->where('teacher_id', $teacherUserId)
            ->with(['student.user', 'student.parent', 'attachments'])
            ->latest('created_at')
            ->get();
    }
}
