<?php

namespace App\Filament\Widgets;

use App\Enums\AttendanceStatus;
use App\Models\Academic\Section;
use App\Models\Academic\StudentEnrollment;
use App\Models\Academic\Teacher;
use App\Models\Schedule\Attendance;
use App\Models\Traits\HasAcademicYear;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SchoolOverviewWidget extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return __('dashboard.widgets.school_overview');
    }

    protected function getDescription(): ?string
    {
        return __('dashboard.widgets.school_overview_description');
    }

    protected int | array | null $columns = ['@xl' => 4, '!@lg' => 2];

    protected function getStats(): array
    {
        $year = HasAcademicYear::getCurrentAcademicYear();

        $students = StudentEnrollment::query()
            ->active()
            ->where('academic_year', $year)
            ->count();

        $teachers = Teacher::query()->count();
        $sections = Section::query()->count();

        $attendanceRecords = Attendance::query()->get();

        $graduated = StudentEnrollment::query()
            ->where('status', 'graduated')
            ->count();

        return [
            Stat::make(__('dashboard.widgets.students'), number_format($students))
                ->icon('heroicon-o-user-group')
                ->color('info'),
            Stat::make(__('dashboard.widgets.teachers'), number_format($teachers))
                ->icon('heroicon-o-academic-cap')
                ->color('success'),
            Stat::make(__('dashboard.widgets.sections'), number_format($sections))
                ->icon('heroicon-o-building-office-2')
                ->color('warning'),
            Stat::make(__('dashboard.widgets.graduated'), $graduated)
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),
        ];
    }
}
