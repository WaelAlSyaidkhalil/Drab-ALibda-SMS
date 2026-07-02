<?php

namespace App\Filament\Resources\TeacherAttendances;

use App\Filament\Resources\TeacherAttendances\Pages\ListTeacherAttendances;
use App\Filament\Resources\TeacherAttendances\Schemas\TeacherAttendanceForm;
use App\Filament\Resources\TeacherAttendances\Tables\TeacherAttendancesTable;
use App\Models\Schedule\TeacherAttendance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeacherAttendanceResource extends Resource
{
    protected static ?string $model = TeacherAttendance::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Teacher Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $navigationLabel = 'Teacher Attendance';

    protected static ?int $navigationSort = 10;
    
    protected static bool $shouldRegisterNavigation = false;


    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.teacher_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.teacher_attendance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.pages.teacher_attendance');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(request('date'), fn ($query) =>
                $query->whereDate('date', request('date'))
            )
            ->when(request('teacher_id'), fn ($query) =>
                $query->where('teacher_id', request('teacher_id'))
            );
    }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record?->teacher?->full_name ?? __('dashboard.labels.unknown');
    }

    public static function form(Schema $schema): Schema
    {
        return TeacherAttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeacherAttendancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeacherAttendances::route('/'),
        ];
    }
}
