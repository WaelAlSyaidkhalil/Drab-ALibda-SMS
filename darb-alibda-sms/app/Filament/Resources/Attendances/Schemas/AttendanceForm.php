<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Enums\AttendanceStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('student_id')
                    ->relationship('student', 'first_name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->full_name
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('status')
                    ->options(AttendanceStatus::options())
                    ->required(),

                DatePicker::make('date')
                    ->required(),

                Textarea::make('reason')
                    ->columnSpanFull()
                    ->maxLength(500)
                    ->placeholder('اختياري...'),
            ]);
        }
}
