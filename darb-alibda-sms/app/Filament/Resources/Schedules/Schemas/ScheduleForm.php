<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Enums\DayOfWeek;
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
                
                Select::make('subject_id')
                    ->label(__('dashboard.labels.subject'))
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.subject_id.required'),
                    ]),

                Select::make('teacher_id')
                    ->label(__('dashboard.labels.teacher'))
                    ->relationship('teacher', 'first_name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.teacher_id.required'),
                    ]),

            ])
            ->columns(2);
    }
}