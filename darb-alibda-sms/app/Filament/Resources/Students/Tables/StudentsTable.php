<?php

namespace App\Filament\Resources\Students\Tables;

use App\Enums\Gender;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
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
                        'father_name',
                        'last_name',
                    ])
                    ->sortable(),

                TextColumn::make('registry_number')
                    ->searchable(),

                TextColumn::make('gender')
                    ->formatStateUsing(fn (string $state) => $state instanceof Gender ? $state->value : $state)
                    ->badge(),

                TextColumn::make('age')
                    ->getStateUsing(fn ($record) => $record->birth_date? now()->diff($record->birth_date)->format('%y') : null)
                    ->placeholder('N/A')
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('parent.is_active')
                    ->boolean()
                    ->label('Active')
                    ])
            ->filters([
                Filter::make('is_active')
                    ->label('Active')
                    ->query(fn ($query) => $query->whereHas('parent', fn ($q) => $q->where('is_active', true))),
                Filter::make('is_inactive')
                    ->label('Inactive')
                    ->query(fn ($query) => $query->whereHas('parent', fn ($q) => $q->where('is_active', false))),
                SelectFilter::make('gender')
                    ->options(Gender::options()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
