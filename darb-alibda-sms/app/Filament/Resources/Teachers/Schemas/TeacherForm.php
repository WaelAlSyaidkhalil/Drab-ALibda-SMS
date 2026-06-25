<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Enums\Gender;
use App\Services\Admin\GeneratePasswordService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Personal Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                fn($state, callable $get, callable $set) =>
                                $set('user.name', trim($state . ' ' . $get('last_name')))
                            ),

                        TextInput::make('last_name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                fn($state, callable $get, callable $set) =>
                                $set('user.name', trim($get('first_name') . ' ' . $state))
                            ),
                        TextInput::make('father_name')
                            ->label('Father Name'),

                        TextInput::make('mother_name')
                            ->label('Mother Name'),

                        DatePicker::make('birth_date')
                            ->label('Birth Date')
                            ->native(false)
                            ->maxDate(now()),

                        Select::make('gender')
                            ->label('Gender')
                            ->options(Gender::options())
                            ->required(),
                    ]),
                Section::make('Official Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('registry_number')
                            ->label('Registry Number'),

                        TextInput::make('national_id')
                            ->label('National ID')
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),
                Section::make('Account Details')
                    ->columns(2)
                    ->relationship('user')
                    ->schema([
                        TextInput::make('name')
                            ->disabled()
                            ->dehydrated(),

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
                            ->default(2),

                        TextInput::make('role_display')
                            ->label('Role')
                            ->placeholder('Teacher')
                            ->disabled(),
                    ]),
                Section::make('Employment Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('employee_number')
                            ->label('Employee Number')
                            ->unique(ignoreRecord: true),

                        DatePicker::make('hire_date')
                            ->label('Hire Date'),

                        TextInput::make('employment_type')
                            ->label('Employment Type'),

                        TextInput::make('grade')
                            ->label('Grade'),

                        TextInput::make('specialization')
                            ->label('Specialization'),

                        TextInput::make('experience_years')
                            ->label('Experience Years')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ]),
                Section::make('Contact Information')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->label('Address')
                            ->columnSpanFull(),

                        TextInput::make('phone_alt')
                            ->label('Alternative Phone')
                            ->tel(),
                    ])
            ]);
    }
}
