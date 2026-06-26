<?php

namespace App\Filament\Resources\StudentEnrollments\Tables;

use App\Enums\MarkResult;
use App\Enums\StudentStatus;
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
                    ->searchable()
                    ->sortable(),

                TextColumn::make('section.full_name')
                    ->label('Class & Section')
                    ->sortable(),

                TextColumn::make('academic_year')
                    ->sortable(),

                TextColumn::make('enrollment_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => StudentStatus::PROMOTED,
                        'warning' => StudentStatus::GRADUATED,
                        'info' => StudentStatus::ACTIVE,
                        'gray' => StudentStatus::REPEATED,
                        'secondary' => StudentStatus::TRANSFERRED,
                        'danger' => StudentStatus::WITHDRAWN,
                    ]),

                TextColumn::make('student_subject_count')
                    ->label('Subjects count'),

                TextColumn::make('final_average_display')
                    ->label('Final average')
                    ->sortable()
                    ->placeholder('N/A'),
                TextColumn::make('final_result')
                    ->getStateUsing(fn($record) => $record->final_result)
                    ->badge()
                    ->colors([
                        'primary' => MarkResult::PENDING,
                        'success' => MarkResult::PASS,
                        'danger' => MarkResult::FAIL,
                    ]),


                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StudentStatus::options()),

                SelectFilter::make('final_result')
                    ->options(MarkResult::options()),

                SelectFilter::make('section')
                    ->relationship('section', 'name'),

                SelectFilter::make('student')
                    ->relationship('student', 'first_name'),
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
