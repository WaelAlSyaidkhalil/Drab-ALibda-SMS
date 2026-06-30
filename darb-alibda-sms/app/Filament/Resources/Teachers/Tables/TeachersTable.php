<?php

namespace App\Filament\Resources\Teachers\Tables;

use Filament\Tables\Table;
use App\Enums\Gender;
use App\Models\Academic\Teacher;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TeachersTable
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

                TextColumn::make('gender')
                    ->label(__('dashboard.labels.gender'))
                    ->formatStateUsing(fn(string $state) => Gender::from($state)->value)
                    ->badge()
                    ->sortable(),

                TextColumn::make('specialization')
                    ->label(__('dashboard.labels.specialization'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('dashboard.labels.not_available')),

                TextColumn::make('experience_years')
                    ->label(__('dashboard.labels.experience_years'))
                    ->sortable()
                    ->placeholder(__('dashboard.labels.not_available')),
                
                IconColumn::make('user.is_active')
                    ->label(__('dashboard.labels.active'))
                    ->boolean()
            ])
            ->filters([
                Filter::make('is_active')
                    ->label(__('dashboard.labels.active'))
                    ->query(fn(Builder $query) => $query->whereHas('user', fn($q) => $q->where('is_active', true))),
                Filter::make('is_inactive')
                    ->label(__('dashboard.labels.inactive'))
                    ->query(fn(Builder $query) => $query->whereHas('user', fn($q) => $q->where('is_active', false))),
                SelectFilter::make('employment_type')
                    ->label(__('dashboard.labels.employment_type'))
                    ->options(
                        fn() => Teacher::query()
                            ->whereNotNull('employment_type')
                            ->distinct()
                            ->pluck('employment_type', 'employment_type')
                            ->toArray()
                    ),
                SelectFilter::make('specialization')
                    ->label(__('dashboard.labels.specialization'))
                    ->options(
                        fn() => Teacher::query()
                            ->whereNotNull('specialization')
                            ->distinct()
                            ->pluck('specialization', 'specialization')
                            ->toArray()
                    ),
                SelectFilter::make('grade')
                    ->label(__('dashboard.labels.grade'))
                    ->options(
                        fn() => Teacher::query()
                            ->whereNotNull('grade')
                            ->distinct()
                            ->pluck('grade', 'grade')
                            ->toArray()
                    ),
                SelectFilter::make('experience_years')
                    ->label(__('dashboard.labels.experience_years'))
                    ->options([
                        '0-5' => __('dashboard.labels.years_0_5'),
                        '6-10' => __('dashboard.labels.years_6_10'),
                        '11-20' => __('dashboard.labels.years_11_20'),
                        '21+' => __('dashboard.labels.years_21_plus'),
                    ])
                    ->modifyQueryUsing(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            '0-5' => $query->whereBetween('experience_years', [0, 5]),
                            '6-10' => $query->whereBetween('experience_years', [6, 10]),
                            '11-20' => $query->whereBetween('experience_years', [11, 20]),
                            '21+' => $query->where('experience_years', '>', 20),
                            default => $query,
                        };
                    })])
            ->actions([
                EditAction::make(),
            ])
            ->defaultSort('employee_number');
    }
}