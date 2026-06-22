<?php

namespace App\Filament\Resources\Schedules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}