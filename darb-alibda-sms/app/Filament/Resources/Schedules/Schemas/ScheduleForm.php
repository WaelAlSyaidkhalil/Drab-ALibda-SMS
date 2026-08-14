<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Enums\DayOfWeek;
use App\Models\Subjects\Subject;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('term_id')
                    ->label(__('dashboard.labels.term'))
                    ->relationship('term', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->academic_year_and_term
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.term_id.required'),
                    ]),

                Select::make('section_id')
                ->label(__('dashboard.labels.section'))
                ->relationship('section', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->validationMessages([
                    'required' => __('validation.custom.section_id.required'),
                ]),

                Select::make('day')
                    ->label(__('dashboard.labels.day'))
                    ->options(DayOfWeek::options())
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.day.required'),
                    ]),

                Select::make('time_slot_id')
                    ->label(__('dashboard.labels.time_slot'))
                    ->relationship('timeSlot', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    ),
                
                Select::make('teacher_id')
                    ->label(__('dashboard.labels.teacher'))
                    ->relationship('teacher', 'first_name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $subjectId = Subject::query()
                            ->where('teacher_id', $state)
                            ->value('id');

                        $set('subject_id', $subjectId);
                    })
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.teacher_id.required'),
                    ]),

                Hidden::make('subject_id')
                    ->default(null),

            ])
            ->columns(2);
    }
}