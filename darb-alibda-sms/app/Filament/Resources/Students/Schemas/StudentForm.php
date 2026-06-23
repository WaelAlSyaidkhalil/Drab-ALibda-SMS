<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Enums\Gender;
use App\Services\Admin\GeneratePasswordService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->options(Gender::options())
                            ->required(),

                        DatePicker::make('birth_date')
                            ->native(false)
                            ->maxDate(now()),
                    ]),
                Section::make('Parent account Details')
                    ->relationship('parent')
                    ->schema([
                        TextInput::make('name')
                            ->label('Parent name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->required()
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->default(fn() => app(GeneratePasswordService::class)->generatePassword()),
                                
                        Hidden::make('role_id')
                            ->default(4),
                            
                        TextInput::make('role_display')
                            ->label('Role')
                            ->placeholder('Parent')
                            ->disabled(),
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
                Section::make('Status')
                    ->relationship('parent')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true),
                    ]),
            ]);
    }
}
