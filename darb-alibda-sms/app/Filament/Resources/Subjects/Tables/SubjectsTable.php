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
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('full_mark')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('pass_mark')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('description')
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