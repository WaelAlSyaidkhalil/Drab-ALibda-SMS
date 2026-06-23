<?php

namespace App\Filament\Resources\Students\Tables;

use App\Enums\Gender;
use Filament\Actions\EditAction;
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

                TextColumn::make('user.is_active')
                    ->getStateUsing(fn ($record) => $record->user?->is_active ? 'active' : 'inactive')
                    ->badge()
                    ->color(fn ($record) => $record->user?->is_active ? 'success' : 'danger')
                    ])
            ->filters([
                Filter::make('is_active')
                    ->label('Active')
                    ->query(fn ($query) => $query->whereHas('user', fn ($q) => $q->where('is_active', true))),

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
