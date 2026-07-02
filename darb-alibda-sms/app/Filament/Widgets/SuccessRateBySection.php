<?php

namespace App\Filament\Widgets;

use App\Enums\MarkResult;
use App\Models\Academic\Section;
use App\Models\Traits\HasAcademicYear;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class SuccessRateBySection extends TableWidget
{
    public function table(Table $table): Table
    {
        $year = HasAcademicYear::getCurrentAcademicYear();

        $sections = Section::query()
            ->with(['enrollments' => fn ($query) => $query->where('academic_year', $year)])
            ->get();

        $rows = $sections->map(function (Section $section) {
            $totalEnrollments = $section->enrollments->count();
            $successfulEnrollments = $section->enrollments
                ->where('final_result', MarkResult::PASS->value)
                ->count();

            $successRate = $totalEnrollments > 0
                ? round(($successfulEnrollments / $totalEnrollments) * 100, 1)
                : 0;

            return [
                'section' => $section->full_name,
                'success_rate' => $successRate,
            ];
        })->sortByDesc('success_rate')->take(4);

        return $table
            ->heading(__('dashboard.widgets.section_success_rate'))
            ->records(fn () => $rows)
            ->columns([
                TextColumn::make('section')
                    ->label(__('dashboard.labels.section')),
                TextColumn::make('success_rate')
                    ->label(__('dashboard.labels.success_rate'))
                    ->formatStateUsing(fn ($state) => $state . '%'),
            ])->pluralModelLabel(__('dashboard.labels.section_success_rate'));
    }
}
