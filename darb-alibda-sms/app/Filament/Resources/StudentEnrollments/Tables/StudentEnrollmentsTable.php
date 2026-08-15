<?php

namespace App\Filament\Resources\StudentEnrollments\Tables;

use App\Enums\MarkResult;
use App\Enums\StudentStatus;
use App\Models\Academic\Student;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentEnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')
                    ->label(__('dashboard.labels.student'))
                    ->searchable(),

                TextColumn::make('section.full_name')
                    ->label(__('dashboard.labels.class_section')),

                TextColumn::make('academic_year')
                    ->label(__('dashboard.labels.academic_year'))
                    ->sortable(),

                TextColumn::make('enrollment_date')
                    ->label(__('dashboard.labels.enrollment_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->colors(StudentStatus::getColors()),

                TextColumn::make('student_subject_count')
                    ->label(__('dashboard.labels.subjects_count')),

                TextColumn::make('final_average')
                    ->label(__('dashboard.labels.final_average'))
                    ->formatStateUsing(fn ($state) => $state === null
                        ? __('dashboard.labels.not_available')
                        : number_format((float) $state, 2) . ' / 100')
                    ->sortable()
                    ->placeholder(__('dashboard.labels.not_available')),
                TextColumn::make('final_result')
                    ->label(__('dashboard.labels.final_result'))
                    ->getStateUsing(fn($record) => $record->final_result)
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->badge()
                    ->colors(MarkResult::getColors()),


                TextColumn::make('created_at')
                    ->label(__('dashboard.labels.created_at'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->options(StudentStatus::options()),

                SelectFilter::make('final_result')
                    ->label(__('dashboard.labels.final_result'))
                    ->options(MarkResult::options()),

                SelectFilter::make('section')
                    ->label(__('dashboard.labels.section'))
                    ->relationship('section', 'name'),
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
