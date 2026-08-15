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
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    )
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
                
                Select::make('subject_id')
                    ->label(__('dashboard.labels.subject'))
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $teacherId = Subject::query()
                            ->whereKey($state)
                            ->value('teacher_id');

                        $set('teacher_id', $teacherId);
                    })
                    ->validationMessages([
                        'required' => __('validation.custom.subject_id.required'),
                    ]),

                Hidden::make('teacher_id')
                    ->default(null),

            ])
            ->columns(2);
    }
}