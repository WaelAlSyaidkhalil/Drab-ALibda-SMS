<?php

namespace App\Filament\Resources\StudentEnrollments\Schemas;

use App\Enums\MarkResult;
use App\Enums\StudentStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentEnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                ->relationship('student', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn ($record) => $record->full_name
                )
                ->searchable()
                ->preload()
                ->required(),

            Select::make('section_id')
                ->relationship('section', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn ($record) => $record->full_name
                )
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('academic_year')
                ->required()
                ->maxLength(20),

            DatePicker::make('enrollment_date')
                ->required(),

            Select::make('status')
                ->options(StudentStatus::options())
                ->required(),

            Select::make('final_result')
                ->options(MarkResult::options())
                ->default('pending')
                ->disabled()
                ->required(),

            TextInput::make('final_average')
                ->label('Final average')
                ->numeric()
                ->disabled()
                ->suffix('%'),
            

            Textarea::make('notes')
                ->columnSpanFull(),
            ]);
    }
}
