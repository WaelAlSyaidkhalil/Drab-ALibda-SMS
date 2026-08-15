<?php

namespace App\Filament\Resources\Schedules\Tables;

use App\Enums\DayOfWeek;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->defaultSort('day')
        ->columns([
                TextColumn::make('term.academic_year_and_term')
                    ->label(__('dashboard.labels.term'))
                    ->sortable(query: function ($query, $direction) {
                        $query
                            ->leftJoin('terms', 'schedules.term_id', '=', 'terms.id')
                            ->orderBy('terms.academic_year', $direction)
                            ->orderByRaw("CASE terms.type
                                WHEN 'First_Term' THEN 1
                                WHEN 'Second_Term' THEN 2
                                ELSE 99
                            END {$direction}");
                    }),

                TextColumn::make('section.full_name')
                    ->label(__('dashboard.labels.section'))
                    ->searchable(),

                TextColumn::make('day')
                    ->label(__('dashboard.labels.day'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->colors(DayOfWeek::getColors())
                    ->sortable(query: function ($query, $direction) {
                        $query->orderByRaw("CASE schedules.day
                            WHEN 'Sun' THEN 1
                            WHEN 'Mon' THEN 2
                            WHEN 'Tue' THEN 3
                            WHEN 'Wed' THEN 4
                            WHEN 'Thu' THEN 5
                            ELSE 99
                        END {$direction}");
                    }),

                TextColumn::make('timeSlot.full_name')
                    ->label(__('dashboard.labels.time_slot'))
                    ->sortable(query: function ($query, $direction) {
                        $query
                            ->leftJoin('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
                            ->orderBy('time_slots.period_number', $direction);
                    }),

                TextColumn::make('subject.name')
                    ->label(__('dashboard.labels.subject'))
                    ->searchable()
                    ->sortable(query: function ($query, $direction) {
                        $query
                            ->leftJoin('subjects', 'schedules.subject_id', '=', 'subjects.id')
                            ->orderBy('subjects.name', $direction);
                    }),

                TextColumn::make('subject.teacher.full_name')
                    ->label(__('dashboard.labels.teacher'))
                    ->searchable()
                    ->sortable(query: function ($query, $direction) {
                        $query
                            ->leftJoin('subjects as schedule_subjects', 'schedules.subject_id', '=', 'schedule_subjects.id')
                            ->leftJoin('teachers', 'schedule_subjects.teacher_id', '=', 'teachers.id')
                            ->orderBy('teachers.first_name', $direction)
                            ->orderBy('teachers.last_name', $direction);
                    }),

                TextColumn::make('created_at')
                    ->label(__('dashboard.labels.created_at'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->modalHeading(__('dashboard.buttons.edit') . ' ' . __('dashboard.pages.schedule')),
                DeleteAction::make()->modalHeading(__('dashboard.buttons.delete') . ' ' . __('dashboard.pages.schedule'))
            ])
            ->toolbarActions([
            ]);
    }
}