<?php

namespace App\Filament\Widgets;

use App\Enums\StudentStatus;
use App\Models\Academic\StudentEnrollment;
use App\Models\Traits\HasAcademicYear;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentStatusWidget extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return __('dashboard.widgets.student_status');
    }

    protected function getDescription(): ?string
    {
        return __('dashboard.widgets.student_status_description');
    }

    protected int | array | null $columns = ['@xl' => 5, '!@lg' => 2];

    protected function getStats(): array
    {
        $year = HasAcademicYear::getCurrentAcademicYear();

        $promoted = StudentEnrollment::query()->where('academic_year', $year)->where('status', StudentStatus::PROMOTED)->count();
        $repeated = StudentEnrollment::query()->where('academic_year', $year)->where('status', StudentStatus::REPEATED)->count();
        $withdrawn = StudentEnrollment::query()->where('academic_year', $year)->where('status', StudentStatus::WITHDRAWN)->count();
        $graduated = StudentEnrollment::query()->where('academic_year', $year)->where('status', StudentStatus::GRADUATED)->count();
        $transferred = StudentEnrollment::query()->where('academic_year', $year)->where('status', StudentStatus::TRANSFERRED)->count();

        return [
            Stat::make(__('dashboard.labels.promoted'), $promoted)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make(__('dashboard.labels.repeated'), $repeated)
                ->icon('heroicon-o-arrow-path')
                ->color('danger'),
            Stat::make(__('dashboard.labels.withdrawn'), $withdrawn)
                ->icon('heroicon-o-x-circle')
                ->color('warning'),
            Stat::make(__('dashboard.labels.graduated'), $graduated)
                ->icon('heroicon-o-academic-cap')
                ->color('info'),
            Stat::make(__('dashboard.labels.transferred'), $transferred)
                ->icon('heroicon-o-arrow-right-circle')
                ->color('secondary'),
        ];
    }
}
