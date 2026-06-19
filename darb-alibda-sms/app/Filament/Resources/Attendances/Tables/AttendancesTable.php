<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Enums\AttendanceStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        AttendanceStatus::PRESENT->value => 'success',
                        AttendanceStatus::ABSENT->value => 'danger',
                        AttendanceStatus::LATE->value => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
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
