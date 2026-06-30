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
                    ->label(__('dashboard.labels.full_name'))
                    ->searchable([
                        'first_name',
                        'father_name',
                        'last_name',
                    ])
                    ->sortable(),

                TextColumn::make('registry_number')
                    ->label(__('dashboard.labels.registry_number'))
                    ->searchable(),

                TextColumn::make('gender')
                    ->label(__('dashboard.labels.gender'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge(),

                TextColumn::make('age')
                    ->label(__('dashboard.labels.age'))
                    ->getStateUsing(fn ($record) => $record->birth_date? now()->diff($record->birth_date)->format('%y') : null)
                    ->placeholder(__('dashboard.labels.not_available'))
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label(__('dashboard.labels.parent_name'))
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('parent.is_active')
                    ->boolean()
                    ->label(__('dashboard.labels.active'))
                    ])
            ->filters([
                Filter::make('is_active')
                    ->label(__('dashboard.labels.active'))
                    ->query(fn ($query) => $query->whereHas('parent', fn ($q) => $q->where('is_active', true))),
                Filter::make('is_inactive')
                    ->label(__('dashboard.labels.inactive'))
                    ->query(fn ($query) => $query->whereHas('parent', fn ($q) => $q->where('is_active', false))),
                SelectFilter::make('gender')
                    ->label(__('dashboard.labels.gender'))
                    ->options(Gender::options()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
