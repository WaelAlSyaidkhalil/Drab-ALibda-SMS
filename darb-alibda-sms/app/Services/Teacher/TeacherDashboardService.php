<?php

namespace App\Services\Teacher;

use App\Repositories\Teacher\TeacherDashboardRepository;

class TeacherDashboardService
{
    public function __construct(protected TeacherDashboardRepository $repository)
    {
    }

    public function getOverview(int $teacherId, int $userId): array
    {
        $presentStudents = $this->repository->countTodayPresentStudents($teacherId);
        $activeStudents = $this->repository->countActiveStudentsForTeacher($teacherId);
        $pendingAbsenceRequests = $this->repository->countPendingAbsenceJustifications();
        $unreadNotes = $this->repository->countUnreadTeacherNotes($userId);
        $announcementsToday = $this->repository->countTodayAnnouncements();

        $attendancePercentage = $activeStudents > 0
            ? round(($presentStudents / $activeStudents) * 100, 2)
            : 0;

        return [
            'present_students_count' => $presentStudents,
            'active_students_count' => $activeStudents,
            'attendance_percentage' => $attendancePercentage,
            'pending_absence_justification_requests_count' => $pendingAbsenceRequests,
            'unread_notes_count' => $unreadNotes,
            'pending_tasks_count' => $pendingAbsenceRequests + $unreadNotes,
            'today_announcements_count' => $announcementsToday,
        ];
    }
}
