<?php

namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->searchable([
                        'first_name',
                        'last_name',
                        'father_name',
                    ])
                    ->sortable(),

                TextColumn::make('registry_number')
                    ->searchable(),

                TextColumn::make('national_id')
                    ->placeholder('N/A')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('gender')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                        default => $state,
                    })
                    ->badge(),

                TextColumn::make('age')
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->icon('heroicon-o-pencil'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
