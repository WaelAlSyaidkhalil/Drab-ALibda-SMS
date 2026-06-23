<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\ManageStudents;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Filament\Resources\Students\Tables\StudentsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Models\Academic\Student;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationLabel = 'Students Info';

    protected static \UnitEnum|string|null $navigationGroup = 'Student Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStudents::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
