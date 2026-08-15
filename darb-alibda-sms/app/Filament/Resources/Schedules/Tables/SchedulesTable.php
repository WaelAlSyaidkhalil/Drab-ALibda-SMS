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
                    ->sortable(),
                    
                TextColumn::make('section.full_name')    
                    ->label(__('dashboard.labels.section'))
                    ->searchable()
                    ->sortable(),    
                                    
                TextColumn::make('day')
                ->label(__('dashboard.labels.day'))
                ->formatStateUsing(fn ($state) => $state->label())
                ->badge()
                ->colors(DayOfWeek::getColors())
                ->sortable(),

                TextColumn::make('timeSlot.full_name')    
                    ->label(__('dashboard.labels.time_slot'))
                    ->sortable(),

                TextColumn::make('subject.name')    
                    ->label(__('dashboard.labels.subject'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject.teacher.full_name')
                    ->label(__('dashboard.labels.teacher'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('dashboard.labels.created_at'))
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
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