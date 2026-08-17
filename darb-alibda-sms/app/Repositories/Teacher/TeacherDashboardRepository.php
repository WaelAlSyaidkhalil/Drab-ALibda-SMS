<?php

namespace App\Repositories\Teacher;

use App\Models\Communication\AbsenceJustification;
use App\Models\Communication\News;
use App\Models\Communication\Message;
use App\Models\Academic\StudentEnrollment;
use App\Models\Schedule\Attendance;
use App\Models\Schedule\Schedule;
use App\Enums\DayOfWeek;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeacherDashboardRepository
{
    public function getSectionIdsForTeacher(int $teacherId)
    {
        return Schedule::query()
            ->whereHas('subject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->pluck('section_id')
            ->unique()
            ->values();
    }

    /**
     * الحصول على يوم الأسبوع الحالي بصيغة الـ enum
     */
    private function getTodayDayOfWeek(): string
    {
        $dayMap = [
            0 => 'Sun',  // الأحد
            1 => 'Mon',  // الاثنين
            2 => 'Tue',  // الثلاثاء
            3 => 'Wed',  // الأربعاء
            4 => 'Thu',  // الخميس
            5 => 'Fri',  // الجمعة
            6 => 'Sat',  // السبت
        ];
        
        return $dayMap[Carbon::today()->dayOfWeek];
    }

    public function countTodayPresentStudents(int $teacherId): int
    {
        $todayDay = $this->getTodayDayOfWeek();

        // الحصول على شعب الحصص المجدولة للمعلم في اليوم الحالي فقط
        $sectionIds = Schedule::query()
            ->whereHas('subject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->where('day', $todayDay)
            ->pluck('section_id')
            ->unique();

        if ($sectionIds->isEmpty()) {
            return 0;
        }

        // حساب الطلاب الحاضرين في هذه الشعب اليوم
        // (جدول الحضور مرتبط بالشعبة section_id وليس بالحصة)
        return Attendance::query()
            ->whereIn('section_id', $sectionIds)
            ->whereDate('date', Carbon::today())
            ->where('status', 'present')
            ->pluck('student_id')
            ->unique()
            ->count();
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
