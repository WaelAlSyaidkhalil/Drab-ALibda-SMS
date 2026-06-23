<?php

namespace App\Filament\Resources\Teachers;

use App\Filament\Resources\Teachers\Pages\ManageTeachers;
use App\Filament\Resources\Teachers\Schemas\TeacherForm;
use App\Filament\Resources\Teachers\Tables\TeachersTable;
use App\Models\Academic\Teacher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Teacher Management';

    protected static ?string $navigationLabel = 'Teachers';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return TeacherForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeachersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTeachers::route('/'),
        ];
    }

        public static function getNavigationBadge(): ?string
        {
            return static::getModel()::count();
        }
}
