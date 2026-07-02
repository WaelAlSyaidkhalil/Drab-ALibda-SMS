<?php

namespace App\Filament\Widgets;

use App\Models\Academic\StudentEnrollment;
use App\Models\Traits\HasAcademicYear;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class TopStudentsWidget extends TableWidget
{

    public function table(Table $table): Table
    {
        $rows = StudentEnrollment::query()
            ->with(['student', 'section'])
            ->where('academic_year', HasAcademicYear::getCurrentAcademicYear())
            ->whereNotNull('final_average')
            ->orderBy('section_id')
            ->orderByDesc('final_average')
            ->get()
            ->groupBy(fn (StudentEnrollment $enrollment) => $enrollment->section?->id ?? 0)
            ->flatMap(function (Collection $sectionEnrollments): Collection {
                return $sectionEnrollments
                    ->take(3)
                    ->values()
                    ->map(function (StudentEnrollment $enrollment, int $index): array {
                        return [
                            'rank' => $index + 1,
                            'student_name' => $enrollment->student?->full_name ?? '—',
                            'section_name' => $enrollment->section?->full_name ?? '—',
                            'final_average' => $enrollment->final_average !== null
                                ? number_format((float) $enrollment->final_average, 1)
                                : '—',
                        ];
                    });
            });

        return $table
            ->heading(__('dashboard.widgets.top_students'))
            ->records(fn () => $rows)
            ->columns([
                TextColumn::make('rank')
                    ->label(__('dashboard.labels.rank'))
                    ->extraAttributes(['class' => 'font-semibold text-gray-700']),
                TextColumn::make('student_name')
                    ->label(__('dashboard.labels.student'))
                    ->extraAttributes(['class' => 'font-semibold text-gray-900'])
                    ->wrap(),
                BadgeColumn::make('section_name')
                    ->label(__('dashboard.labels.section'))
                    ->colors([
                        'primary' => 'section_name:.*',
                    ])
                    ->extraAttributes(['class' => 'font-medium']),
                TextColumn::make('final_average')
                    ->label(__('dashboard.labels.average'))
                    ->extraAttributes(['class' => 'font-semibold text-indigo-600'])
                    ->formatStateUsing(fn ($state): string => (string) $state),
            ])->pluralModelLabel(__('dashboard.labels.top_students'));
    }
}
