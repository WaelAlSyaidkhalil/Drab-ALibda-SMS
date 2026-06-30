<?php

namespace App\Filament\Resources\SchoolClasses;

use App\Filament\Resources\SchoolClasses\Pages\ManageSchoolClasses;
use App\Filament\Resources\SchoolClasses\Pages\ViewSchoolClass;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Models\Academic\SchoolClass;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Override;

class SchoolClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.school_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.school_classes');
    }


    protected static ?int $navigationSort = 0;


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label(__('dashboard.labels.class_type'))
                    ->getStateUsing(fn(SchoolClass $record) => $record->getTypeName())
                    ->sortable()
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchoolClasses::route('/'),
            'view' => ViewSchoolClass::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    #[Override]
    public static function getPluralModelLabel(): string
    {
        return __('dashboard.pages.school_classes');
    }
}
