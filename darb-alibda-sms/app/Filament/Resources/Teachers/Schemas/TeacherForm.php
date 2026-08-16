<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Enums\Gender;
use App\Models\Auth\User;
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
                                'required' => __('dashboard.validation.first_name_required'),
                                'max' => __('dashboard.validation.first_name_max'),
                            ])
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label(__('dashboard.labels.last_name'))
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.last_name_required'),
                                'max' => __('dashboard.validation.last_name_max'),
                            ])
                            ->maxLength(255),

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
                                'required' => __('dashboard.validation.gender_required'),
                            ]),
                    ]),
                Section::make(__('dashboard.labels.account_details'))
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label(__('dashboard.labels.account'))
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query->where('role_id', 2)
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('dashboard.labels.name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label(__('dashboard.labels.phone'))
                                    ->required()
                                    ->tel()
                                    ->unique('users', 'phone')
                                    ->validationMessages([
                                        'required' => __('dashboard.validation.phone_required'),
                                        'unique' => __('dashboard.validation.phone_unique'),
                                        'regex' => __('dashboard.validation.phone_tel'),
                                    ])
                                    ->maxLength(20),

                                TextInput::make('email')
                                    ->label(__('dashboard.labels.email'))
                                    ->required()
                                    ->email()
                                    ->unique('users', 'email')
                                    ->validationMessages([
                                        'required' => __('dashboard.validation.email_required'),
                                        'email' => __('dashboard.validation.email_email'),
                                        'unique' => __('dashboard.validation.email_unique'),
                                    ])
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->label(__('dashboard.labels.password'))
                                    ->password()
                                    ->revealable()
                                    ->default(fn() => app(GeneratePasswordService::class)->generatePassword())
                                    // كلمة المرور مخزّنة مشفّرة، فلا تُعرض عند التعديل.
                                    // اتركها فارغة للإبقاء على كلمة المرور الحالية.
                                    ->formatStateUsing(fn ($state, $operation) => $operation === 'create' ? $state : '')
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn ($operation) => $operation === 'create'),

                                Hidden::make('role_id')
                                    ->default(2),

                                Toggle::make('is_active')
                                    ->label(__('dashboard.labels.active'))
                                    ->default(true)
                                    ->validationMessages([
                                        'boolean' => __('dashboard.validation.is_active_boolean'),
                                    ]),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $user = User::create([
                                    'name' => $data['name'],
                                    'phone' => $data['phone'],
                                    'email' => $data['email'],
                                    'password' => $data['password'],
                                    'role_id' => $data['role_id'],
                                    'is_active' => $data['is_active'] ?? true,
                                ]);

                                return $user->id;
                            }),
                    ]),
                Section::make(__('dashboard.labels.employment_information'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('employee_number')
                            ->label(__('dashboard.labels.employee_number'))
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => __('dashboard.validation.employee_number_unique'),
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
                                'numeric' => __('dashboard.validation.experience_years_numeric'),
                                'min' => __('dashboard.validation.experience_years_min'),
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
                                'required' => __('dashboard.validation.national_id_required'),
                                'unique' => __('dashboard.validation.national_id_unique'),
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
                                'regex' => __('dashboard.validation.phone_tel'),
                            ]),
                    ]),
            ]);
    }
}
