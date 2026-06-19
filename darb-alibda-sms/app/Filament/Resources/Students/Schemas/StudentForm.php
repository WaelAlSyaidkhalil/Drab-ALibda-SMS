<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Grid;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Student Details')
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('father_name')
                            ->maxLength(255),

                        TextInput::make('mother_name')
                            ->maxLength(255),

                        Select::make('gender')
                            ->options([
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                            ])
                            ->required(),

                        DatePicker::make('birth_date')
                            ->native(false)
                            ->maxDate(now()),
                    ]),

                Section::make('Oficial Details')
                    ->schema([
                        TextInput::make('national_id')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        TextInput::make('registry_number')
                            ->required()
                            ->placeholder('Ex: STU001')
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                    ]),
            ]);
    }
}
