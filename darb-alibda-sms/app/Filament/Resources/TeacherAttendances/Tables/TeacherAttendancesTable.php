<?php

namespace App\Filament\Resources\TeacherAttendances\Tables;

use App\Enums\AttendanceStatus;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeacherAttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.full_name')
                    ->label(__('dashboard.labels.full_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label(__('dashboard.labels.date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->colors(AttendanceStatus::getColors()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->options(AttendanceStatus::options()),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
