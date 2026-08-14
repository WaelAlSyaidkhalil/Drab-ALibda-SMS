<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\Auth\User;
use BackedEnum;
use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \UnitEnum|string|null $navigationGroup = 'User Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.user_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.users');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label(__('dashboard.labels.name'))
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.name.required'),
                    ])
                    ->columnSpan(1),

                TextInput::make('email')
                    ->label(__('dashboard.labels.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'required' => __('validation.custom.email.required'),
                        'email' => __('validation.custom.email.email'),
                        'unique' => __('validation.custom.email.unique'),
                    ])
                    ->columnSpan(1),

                TextInput::make('phone')
                    ->label(__('dashboard.labels.phone'))
                    ->tel()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'required' => __('validation.custom.phone.required'),
                        'unique' => __('validation.custom.phone.unique'),
                        'regex' => __('validation.custom.phone.tel'),
                    ])
                    ->columnSpan(1),

                Select::make('role_id')
                    ->label(__('dashboard.labels.role'))
                    ->relationship(
                        name: 'role',
                        titleAttribute: 'name',
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->name->label()
                    )
                    ->required()
                    ->validationMessages([
                        'required' => __('validation.custom.role_id.required'),
                    ]),

                TextInput::make('password')
                    ->label(__('dashboard.labels.password'))
                    ->password()
                    ->formatStateUsing(fn ($state) => '')
                    ->dehydrateStateUsing(function ($state, $record) {
                        if (empty($state)) {
                            return $record->password;
                        }
                        return $record->role->name === UserRole::ADMIN ? Hash::make($state) : $state;
                    })
                    ->required(fn ($operation) => $operation === 'create')
                    ->nullable()
                    ->minLength(8)
                    ->maxLength(255)
                    ->validationMessages([
                        'min' => __('validation.custom.password.min'),
                        'max' => __('validation.custom.password.max'),
                    ])
                    ->columnSpan(1),

                TextInput::make('password_confirmation')
                    ->label(__('dashboard.labels.password_confirmation'))
                    ->dehydrated(false)
                    ->password()
                    ->same('password')
                    ->required(fn ($operation) => $operation === 'create')
                    ->nullable()
                    ->minLength(8)
                    ->maxLength(255)
                    ->columnSpan(1)
                    ->validationMessages([
                        'same' => __('validation.custom.password_confirmation.same'),
                        'min' => __('validation.custom.password_confirmation.min'),
                        'max' => __('validation.custom.password_confirmation.max'),
                    ]),

                Toggle::make('is_active')
                    ->label(__('dashboard.labels.active'))
                    ->default(true)
                    ->hidden(fn ($record) => $record?->role?->name === UserRole::ADMIN)
                    ->validationMessages([
                        'boolean' => __('validation.custom.is_active.boolean'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('dashboard.labels.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('dashboard.labels.email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('dashboard.labels.phone'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('password')
                    ->label(__('dashboard.labels.password'))
                    ->formatStateUsing(fn ($state, $record) => $record->role->name === UserRole::ADMIN ? '********' : $state),
                TextColumn::make('role.name')
                    ->label(__('dashboard.labels.role'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->colors(UserRole::getColors()),
                IconColumn::make('is_active')
                    ->label(__('dashboard.labels.active')),
                TextColumn::make('created_at')
                    ->label(__('dashboard.labels.created_at'))
                    ->dateTime()
                    ->sortable()
                
            ])
            ->filters([
                Filter::make('is_active')
                    ->label(__('dashboard.labels.active'))
                    ->query(fn(Builder $query) => $query->where('is_active', true)),
                SelectFilter::make('role_id')
                    ->label(__('dashboard.labels.role'))
                    ->relationship('role', 'name')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => $record->name->label()
                    )
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.labels.users');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.pages.user');
    }
}
