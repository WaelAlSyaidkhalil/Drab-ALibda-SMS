<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Enums\Gender;
use App\Models\User;
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
                            ->label(__('dashboard.labels.father_name'))
                            ->maxLength(255)
                            ->validationMessages([
                                'max' => __('dashboard.validation.father_name_max'),
                            ]),

                        TextInput::make('mother_name')
                            ->label(__('dashboard.labels.mother_name'))
                            ->maxLength(255)
                            ->validationMessages([
                                'max' => __('dashboard.validation.mother_name_max'),
                            ]),

                        Select::make('gender')
                            ->label(__('dashboard.labels.gender'))
                            ->options(Gender::options())
                            ->required()
                            ->validationMessages([
                                'required' => __('dashboard.validation.gender_required'),
                            ]),

                        DatePicker::make('birth_date')
                            ->label(__('dashboard.labels.birth_date'))
                            ->native(false)
                            ->maxDate(now()),
                    ])->columns(2),
               Section::make(__('dashboard.labels.parent_account_details'))
                    ->schema([
                        Select::make('parent_id')
                            ->label(__('dashboard.labels.parent_name'))
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->where('role_id', 4)
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label(__('dashboard.labels.parent_name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label(__('dashboard.labels.phone'))
                                    ->required()
                                    ->tel()
                                    ->unique('users', 'phone')
                                    ->maxLength(20),

                                TextInput::make('email')
                                    ->label(__('dashboard.labels.email'))
                                    ->email()
                                    ->unique('users', 'email')
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->label(__('dashboard.labels.password'))
                                    ->password()
                                    ->revealable()
                                    ->default(fn () => app(GeneratePasswordService::class)->generatePassword())
                                    // كلمة المرور مخزّنة مشفّرة، فلا تُعرض عند التعديل.
                                    // اتركها فارغة للإبقاء على كلمة المرور الحالية.
                                    ->formatStateUsing(fn ($state, $operation) => $operation === 'create' ? $state : '')
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn ($operation) => $operation === 'create'),

                                Hidden::make('role_id')
                                    ->default(4),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $parent = User::create([
                                    'name' => $data['name'],
                                    'phone' => $data['phone'],
                                    'email' => $data['email'],
                                    'password' => app(GeneratePasswordService::class)->generatePassword(),
                                    'role_id' => $data['role_id'],
                                ]);

                                return $parent->id;
                            }),
                    ]),
                Section::make(__('dashboard.labels.official_details'))
                    ->schema([
                        TextInput::make('national_id')
                            ->label(__('dashboard.labels.national_id'))
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => __('dashboard.validation.national_id_unique'),
                            ])
                            ->maxLength(50),

                        TextInput::make('registry_number')
                            ->label(__('dashboard.labels.registry_number'))
                            ->required()
                            ->placeholder(__('dashboard.labels.not_available'))
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => __('dashboard.validation.registry_number_required'),
                                'unique' => __('dashboard.validation.registry_number_unique'),
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
                                'boolean' => __('dashboard.validation.is_active_boolean'),
                            ]),
                    ]),
            ])->columns(1);
    }
}
