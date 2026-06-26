<?php

namespace App\Http\Controllers\Parent;
use App\Models\Academic\Section;
use App\Models\Academic\Student;
use App\Models\Academic\StudentEnrollment;
use App\Models\Schedule\Attendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends ParentController
{
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $attendances = Attendance::where('student_id', $student->id)->get();

        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $excusedDays = $attendances->where('status', 'excused')->count();

        $attendancePercentage = $totalDays > 0 
            ? round(($presentDays / $totalDays) * 100, 2) 
            : 0;

        return $this->successResponse([
            'summary' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'excused_days' => $excusedDays,
                'attendance_percentage' => $attendancePercentage,
                'attendance_status' => $this->getAttendanceStatus($attendancePercentage),
            ],
            'recent_attendance' => $this->getRecentAttendance($student),
            'monthly_attendance' => $this->getMonthlyAttendance($student),
        ], 'تم جلب بيانات الحضور بنجاح.');
    }

    /**
     * عرض تقرير كامل للحضور مع ترتيب الفصل
     */
    public function report(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $attendances = Attendance::where('student_id', $student->id)->get();

        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $excusedDays = $attendances->where('status', 'excused')->count();

        $attendancePercentage = $totalDays > 0 
            ? round(($presentDays / $totalDays) * 100, 2) 
            : 0;

        // حساب ترتيب الطالب في الفصل
        $classRank = $this->calculateClassRank($student);

        return $this->successResponse([
            'student' => [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->first_name . ' ' . $student->last_name,
            ],
            'summary' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'excused_days' => $excusedDays,
                'attendance_percentage' => $attendancePercentage,
                'attendance_status' => $this->getAttendanceStatus($attendancePercentage),
            ],
            'class_rank' => $classRank,
            'absent_dates' => $attendances
                ->where('status', 'absent')
                ->pluck('date')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->toArray(),
            'late_dates' => $attendances
                ->where('status', 'late')
                ->pluck('date')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->toArray(),
            'excused_dates' => $attendances
                ->where('status', 'excused')
                ->pluck('date')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->toArray(),
            'recommendations' => $this->getRecommendations($presentDays, $totalDays),
        ], 'تم جلب تقرير الحضور بنجاح.');
    }

    /**
     * حساب ترتيب الطالب في الفصل باستخدام StudentEnrollment
     */
    private function calculateClassRank(Student $student): array
    {
        // جلب التسجيل الحالي للطالب (آخر تسجيل نشط)
        $currentEnrollment = StudentEnrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->orderBy('enrollment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$currentEnrollment) {
            return [
                'rank' => null,
                'total_students' => 0,
                'students_better' => 0,
                'top_student' => null,
                'top_student_percentage' => 0,
                'average_percentage' => 0,
                'class_name' => null,
                'section_name' => null,
                'academic_year' => null,
                'message' => 'الطالب غير مسجل في أي فصل',
            ];
        }

        // جلب الشعبة
        $section = Section::with('schoolClass')->find($currentEnrollment->section_id);
        
        if (!$section) {
            return [
                'rank' => null,
                'total_students' => 0,
                'students_better' => 0,
                'top_student' => null,
                'top_student_percentage' => 0,
                'average_percentage' => 0,
                'class_name' => null,
                'section_name' => null,
                'academic_year' => $currentEnrollment->academic_year,
                'message' => 'الشعبة غير موجودة',
            ];
        }

        // جلب جميع الطلاب المسجلين في نفس الشعبة
        $classStudents = StudentEnrollment::where('section_id', $currentEnrollment->section_id)
            ->where('academic_year', $currentEnrollment->academic_year)
            ->where('status', 'active')
            ->with('student')
            ->get();

        if ($classStudents->isEmpty()) {
            return [
                'rank' => null,
                'total_students' => 0,
                'students_better' => 0,
                'top_student' => null,
                'top_student_percentage' => 0,
                'average_percentage' => 0,
                'class_name' => $section->schoolClass?->name ?? 'غير محدد',
                'section_name' => $section->name ?? 'غير محدد',
                'academic_year' => $currentEnrollment->academic_year,
                'message' => 'لا يوجد طلاب آخرون في هذه الشعبة',
            ];
        }

        // حساب نسبة الحضور لكل طالب
        $ranking = [];
        
        foreach ($classStudents as $enrollment) {
            $studentData = $enrollment->student;
            if ($studentData) {
                $attendances = Attendance::where('student_id', $studentData->id)->get();
                $total = $attendances->count();
                $present = $attendances->where('status', 'present')->count();
                $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                
                $ranking[] = [
                    'student_id' => $studentData->id,
                    'student_name' => $studentData->first_name . ' ' . $studentData->last_name,
                    'percentage' => $percentage,
                    'total_days' => $total,
                    'present_days' => $present,
                ];
            }
        }

        // إذا لم يتم بناء الـ ranking بشكل صحيح
        if (empty($ranking)) {
            return [
                'rank' => null,
                'total_students' => 0,
                'students_better' => 0,
                'top_student' => null,
                'top_student_percentage' => 0,
                'average_percentage' => 0,
                'class_name' => $section->schoolClass?->name ?? 'غير محدد',
                'section_name' => $section->name ?? 'غير محدد',
                'academic_year' => $currentEnrollment->academic_year,
                'message' => 'لا توجد بيانات كافية لحساب الترتيب',
            ];
        }

        // ترتيب تنازلي حسب النسبة
        usort($ranking, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        // معرفة ترتيب الطالب
        $studentRank = array_search($student->id, array_column($ranking, 'student_id'));

        // حساب متوسط الفصل
        $averagePercentage = count($ranking) > 0 
            ? round(array_sum(array_column($ranking, 'percentage')) / count($ranking), 2) 
            : 0;

        // حساب عدد الطلاب الذين تفوقوا على هذا الطالب
        $studentsBetter = 0;
        if ($studentRank !== false) {
            foreach ($ranking as $index => $item) {
                if ($index < $studentRank) {
                    $studentsBetter++;
                }
            }
        }

        return [
            'rank' => $studentRank !== false ? $studentRank + 1 : null,
            'total_students' => count($ranking),
            'students_better' => $studentsBetter,
            'top_student' => !empty($ranking) ? $ranking[0]['student_name'] : null,
            'top_student_percentage' => !empty($ranking) ? $ranking[0]['percentage'] : 0,
            'average_percentage' => $averagePercentage,
            'class_name' => $section->schoolClass?->name ?? 'غير محدد',
            'section_name' => $section->name ?? 'غير محدد',
            'academic_year' => $currentEnrollment->academic_year,
            'message' => $studentRank !== false ? 'تم حساب الترتيب بنجاح' : 'لم يتم تحديد الترتيب',
        ];
    }

    /**
     * جلب سجل الحضور للأيام الأخيرة
     */
    private function getRecentAttendance(Student $student, int $limit = 10): array
    {
        return Attendance::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($attendance) {
                return [
                    'date' => $attendance->date->format('Y-m-d'),
                    'status' => $attendance->status,
                    'status_label' => $attendance->status_label,
                    'icon' => $attendance->icon,
                    'reason' => $attendance->reason,
                    'schedule_id' => $attendance->schedule_id,
                ];
            })
            ->toArray();
    }

    /**
     * جلب الحضور الشهري
     */
    private function getMonthlyAttendance(Student $student): array
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $dailyStats = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            
            if ($currentDate->dayOfWeek !== Carbon::FRIDAY && $currentDate->dayOfWeek !== Carbon::SATURDAY) {
                $attendance = $attendances->firstWhere('date', $dateStr);
                
                $dailyStats[] = [
                    'date' => $dateStr,
                    'day_name' => $this->getArabicDayName($currentDate->dayOfWeek),
                    'status' => $attendance ? $attendance->status : 'not_recorded',
                    'status_label' => $attendance ? $attendance->status_label : 'غير مسجل',
                    'icon' => $attendance ? $attendance->icon : '❓',
                    'is_weekend' => false,
                ];
            } else {
                $dailyStats[] = [
                    'date' => $dateStr,
                    'day_name' => $this->getArabicDayName($currentDate->dayOfWeek),
                    'status' => 'weekend',
                    'status_label' => 'عطلة',
                    'icon' => '📅',
                    'is_weekend' => true,
                ];
            }
            
            $currentDate->addDay();
        }

        return $dailyStats;
    }

    /**
     * جلب اسم اليوم بالعربية
     */
    private function getArabicDayName(int $dayOfWeek): string
    {
        $days = [
            Carbon::SUNDAY => 'الأحد',
            Carbon::MONDAY => 'الإثنين',
            Carbon::TUESDAY => 'الثلاثاء',
            Carbon::WEDNESDAY => 'الأربعاء',
            Carbon::THURSDAY => 'الخميس',
            Carbon::FRIDAY => 'الجمعة',
            Carbon::SATURDAY => 'السبت',
        ];

        return $days[$dayOfWeek] ?? '';
    }

    /**
     * تحديد حالة الحضور العامة
     */
    private function getAttendanceStatus(float $percentage): string
    {
        if ($percentage >= 90) {
            return 'ممتاز 🌟';
        } elseif ($percentage >= 75) {
            return 'جيد جداً 👍';
        } elseif ($percentage >= 60) {
            return 'جيد 📚';
        } elseif ($percentage >= 40) {
            return 'مقبول ⚠️';
        } else {
            return 'ضعيف 🚨';
        }
    }

    /**
     * تقديم توصيات بناءً على نسبة الحضور
     */
    private function getRecommendations(int $presentDays, int $totalDays): array
    {
        $percentage = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
        
        $recommendations = [];

        if ($percentage < 75) {
            $recommendations[] = '⚠️ نسبة الحضور أقل من 75%. يرجى متابعة حضور الطالب بانتظام.';
        }

        if ($percentage < 60) {
            $recommendations[] = '🚨 الطالب معرض للرسوب بسبب كثرة الغياب. يرجى التواصل مع المدرسة.';
        }

        if ($percentage >= 90) {
            $recommendations[] = '🌟 نسبة حضور ممتازة. شكراً لمتابعتكم المستمرة.';
        }

        if (empty($recommendations)) {
            $recommendations[] = '📚 نسبة الحضور جيدة. استمروا في المتابعة.';
        }

        return $recommendations;
    }

    /**
     * عرض إحصائيات الحضور حسب الشهر
     */
    public function monthlyStats(Request $request, Student $student, int $month, int $year): JsonResponse
    {
        $this->authorize('view', $student);

        $attendances = Attendance::where('student_id', $student->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $excusedDays = $attendances->where('status', 'excused')->count();

        $attendancePercentage = $totalDays > 0 
            ? round(($presentDays / $totalDays) * 100, 2) 
            : 0;

        return $this->successResponse([
            'month' => $month,
            'year' => $year,
            'month_name' => Carbon::create($year, $month, 1)->translatedFormat('F'),
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'excused_days' => $excusedDays,
            'attendance_percentage' => $attendancePercentage,
            'attendance_status' => $this->getAttendanceStatus($attendancePercentage),
        ], 'تم جلب إحصائيات الحضور الشهرية بنجاح.');
    }
}