<?php

namespace App\Filament\Resources\Subjects\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('dashboard.labels.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label(__('dashboard.labels.code'))
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('schoolClass.type')
                    ->label(__('dashboard.labels.class'))
                    ->formatStateUsing(fn($state) => $state->label())
                    ->sortable(),

                TextColumn::make('teacher.full_name')
                    ->label(__('dashboard.labels.teacher'))
                    ->searchable([
                        'first_name',
                        'father_name',
                        'last_name',
                    ]),

                TextColumn::make('full_mark')
                    ->label(__('dashboard.labels.full_mark'))
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('pass_mark')
                    ->label(__('dashboard.labels.pass_mark'))
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('description')
                    ->label(__('dashboard.labels.description'))
                    ->limit(50)
                    ->toggleable(),
            ])
            ->headerActions([
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}