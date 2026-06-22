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
                    ->relationship('term', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->academic_year_and_term
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('section_id')
                ->relationship('section', 'name')
                ->searchable()
                ->preload()
                ->required(),

                Select::make('day')
                    ->options(DayOfWeek::getValues())
                    ->required(),

                Select::make('time_slot_id')
                    ->relationship('timeSlot', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    ),
                
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('teacher_id')
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