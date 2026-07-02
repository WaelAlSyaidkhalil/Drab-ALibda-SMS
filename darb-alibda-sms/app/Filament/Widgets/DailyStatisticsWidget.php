<?php

namespace App\Filament\Widgets;

use App\Enums\AttendanceStatus;
use App\Enums\ComplaintStatus;
use App\Models\Academic\Teacher;
use App\Models\Schedule\Attendance;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Communication\Complaint;
use App\Models\Communication\Suggestion;
use App\Models\Schedule\TeacherAttendance;

class DailyStatisticsWidget extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return __('dashboard.widgets.daily_statistics');
    }

    protected function getDescription(): ?string
    {
        return __('dashboard.widgets.daily_statistics_description');
    }

    protected int | array | null $columns = ['@xl' => 4, '!@lg' => 2];

    protected function getStats(): array
    {
        $today = Carbon::today();

        $studentsAbsent = Attendance::query()
            ->whereDate('date', $today)
            ->absent()
            ->count();

        $teachersAbsent = TeacherAttendance::query()->where('date', $today)->where('status', AttendanceStatus::ABSENT)->count();
        $newSuggestions = Suggestion::query()->where('is_acknowledged', false)->count();
        $newComplaints = Complaint::query()->where('status', ComplaintStatus::PENDING)->count();

        return [
            Stat::make(__('dashboard.widgets.students_absent'), $studentsAbsent)
                ->icon('heroicon-o-user-minus')
                ->color('warning'),
            Stat::make(__('dashboard.widgets.teachers_absent'), $teachersAbsent)
                ->icon('heroicon-o-user-circle')
                ->color('danger'),
            Stat::make(__('dashboard.widgets.new_suggestions'), $newSuggestions)
                ->icon('heroicon-o-light-bulb')
                ->color('info'),
            Stat::make(__('dashboard.widgets.new_complaints'), $newComplaints)
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('purple'),
        ];
    }
}
