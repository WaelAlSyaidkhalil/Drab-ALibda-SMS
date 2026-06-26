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
                    ->label('Term')
                    ->sortable(),
                    
                TextColumn::make('section.full_name')    
                    ->searchable()
                    ->sortable(),    
                                    
                TextColumn::make('day')
                ->badge()
                ->colors([
                    'danger' => DayOfWeek::SUNDAY,
                    'gray' => DayOfWeek::MONDAY,
                    'success' => DayOfWeek::TUESDAY,
                    'warning' => DayOfWeek::WEDNESDAY,
                    'info' => DayOfWeek::THURSDAY,
                ])
                ->sortable(),

                TextColumn::make('timeSlot.full_name')    
                    ->sortable(),

                TextColumn::make('subject.name')    
                    ->searchable()
                    ->sortable(),

                TextColumn::make('teacher.full_name')    
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
            ]);
    }
}