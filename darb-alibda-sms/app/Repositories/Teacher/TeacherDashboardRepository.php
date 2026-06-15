<?php

namespace App\Repositories\Teacher;

use App\Models\Communication\AbsenceJustification;
use App\Models\Communication\News;
use App\Models\Communication\Message;
use App\Models\Academic\StudentEnrollment;
use App\Models\Schedule\Attendance;
use App\Models\Schedule\Schedule;
use Carbon\Carbon;

class TeacherDashboardRepository
{
    public function getSectionIdsForTeacher(int $teacherId)
    {
        return Schedule::query()
            ->where('teacher_id', $teacherId)
            ->pluck('section_id')
            ->unique()
            ->values();
    }

    public function countTodayPresentStudents(int $teacherId): int
    {
        return Attendance::query()
            ->whereHas('schedule', fn ($query) => $query->where('teacher_id', $teacherId))
            ->whereDate('date', Carbon::today())
            ->where('status', 'present')
            ->distinct('student_id')
            ->count('student_id');
    }

    public function countActiveStudentsForTeacher(int $teacherId): int
    {
        $sectionIds = $this->getSectionIdsForTeacher($teacherId);

        if ($sectionIds->isEmpty()) {
            return 0;
        }

        return StudentEnrollment::query()
            ->whereIn('section_id', $sectionIds)
            ->where('status', 'active')
            ->count();
    }

    public function countPendingAbsenceJustifications(): int
    {
        return AbsenceJustification::query()
            ->pending()
            ->count();
    }

    public function countUnreadTeacherNotes(int $userId): int
    {
        return Message::query()
            ->where('is_read', false)
            ->where('sender_id', '!=', $userId)
            ->whereHas('conversation', fn ($query) =>
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId)
            )
            ->count();
    }

    public function countTodayAnnouncements(): int
    {
        return News::query()
            ->forTeachers()
            ->whereDate('created_at', Carbon::today())
            ->count();
    }
}
