<?php

namespace App\Filament\Widgets;

use App\Models\Grading\StudentSubjectResult;
use App\Models\Traits\HasAcademicYear;
use Filament\Widgets\ChartWidget;

class SubjectSuccessChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('dashboard.widgets.subject_success_chart');
    }

    public function getDescription(): ?string
    {
        return __('dashboard.widgets.subject_success_chart_description');
    }

    protected ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $year = HasAcademicYear::getCurrentAcademicYear();

        $results = StudentSubjectResult::query()
            ->whereHas('enrollment', fn ($query) => $query->where('academic_year', $year))
            ->with('subject')
            ->get();

        $grouped = $results->groupBy('subject_id');
        $subjects = $grouped->map(function ($items) {
            $subject = $items->first()->subject;
            $average = $items->whereNotNull('yearly_mark')->avg('yearly_mark');
            if($average === null) {
                $average = $items->whereNotNull('term1_mark')->avg('term1_mark');
            }
            return [
                'label' => $subject?->name ?? 'Unknown',
                'value' => round($average ?? 0, 1),
            ];
        })->sortByDesc('value')->take(6);

        return [
            'labels' => $subjects->pluck('label')->all(),
            'datasets' => [[
                'label' => __('dashboard.widgets.average'),
                'data' => $subjects->pluck('value')->all(),
                'backgroundColor' => ['#4f46e5', '#ec4899', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6'],
            ]],
        ];
    }
}
