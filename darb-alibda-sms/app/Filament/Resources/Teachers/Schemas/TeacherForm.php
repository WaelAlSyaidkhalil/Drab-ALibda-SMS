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
                Section::make(__('dashboard.labels.personal_information'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('dashboard.labels.first_name'))
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.first_name.required'),
                                'max' => __('validation.custom.first_name.max'),
                            ])
                            ->live()
                            ->afterStateUpdated(
                                fn($state, callable $get, callable $set) =>
                                $set('user.name', trim($state . ' ' . $get('last_name')))
                            ),

                        TextInput::make('last_name')
                            ->label(__('dashboard.labels.last_name'))
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.last_name.required'),
                                'max' => __('validation.custom.last_name.max'),
                            ])
                            ->live()
                            ->afterStateUpdated(
                                fn($state, callable $get, callable $set) =>
                                $set('user.name', trim($get('first_name') . ' ' . $state))
                            ),
                        TextInput::make('father_name')
                            ->label(__('dashboard.labels.father_name')),

                        TextInput::make('mother_name')
                            ->label(__('dashboard.labels.mother_name')),

                        DatePicker::make('birth_date')
                            ->label(__('dashboard.labels.birth_date'))
                            ->native(false)
                            ->maxDate(now()),

                        Select::make('gender')
                            ->label(__('dashboard.labels.gender'))
                            ->options(Gender::options())
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.gender.required'),
                            ]),
                    ]),
                Section::make(__('dashboard.labels.account_details'))
                    ->columns(2)
                    ->relationship('user')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('dashboard.labels.name'))
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('phone')
                            ->label(__('dashboard.labels.phone'))
                            ->required()
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => __('validation.custom.phone.required'),
                                'unique' => __('validation.custom.phone.unique'),
                                'regex' => __('validation.custom.phone.tel'),
                            ])
                            ->maxLength(20),

                        TextInput::make('email')
                            ->label(__('dashboard.labels.email'))
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'email' => __('validation.custom.email.email'),
                                'unique' => __('validation.custom.email.unique'),
                            ])
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label(__('dashboard.labels.password'))
                            ->default(fn() => app(GeneratePasswordService::class)->generatePassword()),

                        Hidden::make('role_id')
                            ->default(2),

                        TextInput::make('role_display')
                            ->label(__('dashboard.labels.role'))
                            ->placeholder(__('dashboard.labels.teacher'))
                            ->disabled(),
                    ]),
                Section::make(__('dashboard.labels.employment_information'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('employee_number')
                            ->label(__('dashboard.labels.employee_number'))
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => __('validation.custom.employee_number.unique'),
                            ]),

                        DatePicker::make('hire_date')
                            ->label(__('dashboard.labels.hire_date')),

                        TextInput::make('employment_type')
                            ->label(__('dashboard.labels.employment_type')),

                        TextInput::make('grade')
                            ->label(__('dashboard.labels.grade')),

                        TextInput::make('specialization')
                            ->label(__('dashboard.labels.specialization')),

                        TextInput::make('experience_years')
                            ->label(__('dashboard.labels.experience_years'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->validationMessages([
                                'numeric' => __('validation.custom.experience_years.numeric'),
                                'min' => __('validation.custom.experience_years.min'),
                            ]),
                    ]),
                Section::make(__('dashboard.labels.official_information'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('registry_number')
                            ->label(__('dashboard.labels.registry_number')),

                        TextInput::make('national_id')
                            ->label(__('dashboard.labels.national_id'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => __('validation.custom.national_id.required'),
                                'unique' => __('validation.custom.national_id.unique'),
                            ]),
                    ]),
                Section::make(__('dashboard.labels.contact_information'))
                    ->columns(2)
                    ->schema([
                        Textarea::make('address')
                            ->label(__('dashboard.labels.address'))
                            ->columnSpanFull(),

                        TextInput::make('phone_alt')
                            ->label(__('dashboard.labels.alternative_phone'))
                            ->tel()
                            ->validationMessages([
                                'regex' => __('validation.custom.phone.tel'),
                            ])
                    ]),
                Section::make(__('dashboard.labels.status'))
                    ->relationship('user')
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('dashboard.labels.active'))
                            ->default(true)
                            ->validationMessages([
                                'boolean' => __('validation.custom.is_active.boolean'),
                            ]),
                    ]),
            ]);
    }
}
