<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\ManageRoles;
use App\Models\Auth\Role;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static \UnitEnum|string|null $navigationGroup = 'User Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Key;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.user_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.roles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Textarea::make('description')
                    ->label(__('dashboard.labels.description'))
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('dashboard.labels.name'))
                    ->formatStateUsing(fn($state) => $state->label())
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('dashboard.labels.description'))
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()->modalHeading(fn($record) => __('dashboard.buttons.edit') . ' ' . $record->name->label()),
                DeleteAction::make()->modalHeading(fn($record) => __('dashboard.buttons.delete') . ' ' . $record->name->label()),
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
            'index' => ManageRoles::route('/'),
        ];
    }
}
