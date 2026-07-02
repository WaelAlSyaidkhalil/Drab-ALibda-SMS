<?php

namespace App\Filament\Widgets;

use App\Enums\MarkResult;
use App\Models\Academic\StudentEnrollment;
use App\Models\Schedule\Attendance;
use App\Models\Traits\HasAcademicYear;
use Filament\Widgets\ChartWidget;

class SchoolRatesWidget extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('dashboard.widgets.school_rates');
    }

    public function getDescription(): ?string
    {
        return __('dashboard.widgets.school_rates_description');
    }

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $year = HasAcademicYear::getCurrentAcademicYear();

        $enrollments = StudentEnrollment::query()->where('academic_year', $year)->get();
        $finalized = $enrollments->whereIn('final_result', [MarkResult::PASS, MarkResult::FAIL]);
        $successRate = $finalized->isEmpty()
            ? 0
            : round(($finalized->where('final_result', MarkResult::PASS)->count() / $finalized->count()) * 100);
    
        return [
            'labels' => [__('dashboard.widgets.success_rate'), __('dashboard.widgets.failure_rate')],
            'datasets' => [[
                'label' => __('dashboard.widgets.success_rate'),
                'data' => [$successRate, 100 - $successRate],
                'backgroundColor' => ['#10b981', '#ef4444'],
            ]],
        ];
    }
}
