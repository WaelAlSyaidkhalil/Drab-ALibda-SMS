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
                    ->searchable([
                        'first_name',
                        'father_name',
                        'last_name',
                    ])
                    ->sortable(),

                TextColumn::make('gender')
                    ->formatStateUsing(fn(string $state) => Gender::from($state)->value)
                    ->badge()
                    ->sortable(),

                TextColumn::make('specialization')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),

                TextColumn::make('experience_years')
                    ->label('Experience Years')
                    ->sortable()
                    ->placeholder('N/A'),
                
                IconColumn::make('user.is_active')
                    ->label('Active')
                    ->boolean()
            ])
            ->filters([
                Filter::make('is_active')
                    ->label('Active')
                    ->query(fn(Builder $query) => $query->whereHas('user', fn($q) => $q->where('is_active', true))),
                Filter::make('is_inactive')
                    ->label('Inactive')
                    ->query(fn(Builder $query) => $query->whereHas('user', fn($q) => $q->where('is_active', false))),
                SelectFilter::make('employment_type')
                    ->options(
                        fn() => Teacher::query()
                            ->whereNotNull('employment_type')
                            ->distinct()
                            ->pluck('employment_type', 'employment_type')
                            ->toArray()
                    ),
                SelectFilter::make('specialization')
                    ->options(
                        fn() => Teacher::query()
                            ->whereNotNull('specialization')
                            ->distinct()
                            ->pluck('specialization', 'specialization')
                            ->toArray()
                    ),
                SelectFilter::make('grade')
                    ->options(
                        fn() => Teacher::query()
                            ->whereNotNull('grade')
                            ->distinct()
                            ->pluck('grade', 'grade')
                            ->toArray()
                    ),
                SelectFilter::make('experience_years')
                    ->label('Experience Years')
                    ->options([
                        '0-5' => '0-5 years',
                        '6-10' => '6-10 years',
                        '11-20' => '11-20 years',
                        '21+' => '21+ years',
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