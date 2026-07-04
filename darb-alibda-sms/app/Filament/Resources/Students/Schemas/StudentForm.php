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
                Section::make(__('dashboard.labels.student_details'))
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('dashboard.labels.first_name'))
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.first_name.required'),
                                'max' => __('validation.custom.first_name.max'),
                            ])
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label(__('dashboard.labels.last_name'))
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.last_name.required'),
                                'max' => __('validation.custom.last_name.max'),
                            ])
                            ->maxLength(255),

                        TextInput::make('father_name')
                            ->label(__('dashboard.labels.father_name'))
                            ->maxLength(255)
                            ->validationMessages([
                                'max' => __('validation.custom.father_name.max'),
                            ]),

                        TextInput::make('mother_name')
                            ->label(__('dashboard.labels.mother_name'))
                            ->maxLength(255)
                            ->validationMessages([
                                'max' => __('validation.custom.mother_name.max'),
                            ]),

                        Select::make('gender')
                            ->label(__('dashboard.labels.gender'))
                            ->options(Gender::options())
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.gender.required'),
                            ]),

                        DatePicker::make('birth_date')
                            ->label(__('dashboard.labels.birth_date'))
                            ->native(false)
                            ->maxDate(now()),
                    ])->columns(2),
                Section::make(__('dashboard.labels.parent_account_details'))
                    ->relationship('parent')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('dashboard.labels.parent_name'))
                            ->required()
                            ->validationMessages([
                                'required' => __('validation.custom.name.required'),
                                'max' => __('validation.custom.name.max'),
                            ])
                            ->maxLength(255),

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
                            ->default(4),

                        TextInput::make('role_display')
                            ->label(__('dashboard.labels.role'))
                            ->placeholder(__(''))
                            ->disabled()
                            ->validationMessages([
                                'required' => __('validation.custom.role_display.required'),
                            ])
                    ])->columns(2),

                Section::make(__('dashboard.labels.official_details'))
                    ->schema([
                        TextInput::make('national_id')
                            ->label(__('dashboard.labels.national_id'))
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => __('validation.custom.national_id.unique'),
                            ])
                            ->maxLength(50),

                        TextInput::make('registry_number')
                            ->label(__('dashboard.labels.registry_number'))
                            ->required()
                            ->placeholder(__('dashboard.labels.not_available'))
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => __('validation.custom.registry_number.required'),
                                'unique' => __('validation.custom.registry_number.unique'),
                            ])
                            ->maxLength(50),
                    ])->columns(2),
                Section::make(__('dashboard.labels.status'))
                    ->relationship('parent')
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('dashboard.labels.active'))
                            ->default(true)
                            ->validationMessages([
                                'boolean' => __('validation.custom.is_active.boolean'),
                            ]),
                    ]),
            ])->columns(1);
    }
}
