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
                    ->required(),

                Select::make('section_id')
                ->label(__('dashboard.labels.section'))
                ->relationship('section', 'name')
                ->searchable()
                ->preload()
                ->required(),

                Select::make('day')
                    ->label(__('dashboard.labels.day'))
                    ->options(DayOfWeek::options())
                    ->required(),

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
                    ->required(),

                Select::make('teacher_id')
                    ->label(__('dashboard.labels.teacher'))
                    ->relationship('teacher', 'first_name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

            ])
            ->columns(2);
    }
}