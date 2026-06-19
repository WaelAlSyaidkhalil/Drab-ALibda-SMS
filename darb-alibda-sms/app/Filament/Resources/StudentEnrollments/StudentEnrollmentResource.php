<?php

namespace App\Filament\Resources\StudentEnrollments;

use App\Filament\Resources\StudentEnrollments\Pages\CreateStudentEnrollment;
use App\Filament\Resources\StudentEnrollments\Pages\EditStudentEnrollment;
use App\Filament\Resources\StudentEnrollments\Pages\ListStudentEnrollments;
use App\Filament\Resources\StudentEnrollments\Pages\ViewStudentEnrollment;
use App\Filament\Resources\StudentEnrollments\Schemas\StudentEnrollmentForm;
use App\Filament\Resources\StudentEnrollments\Tables\StudentEnrollmentsTable;
use App\Filament\Resources\Students\RelationManagers\MarksRelationManager;
use App\Filament\Resources\Students\RelationManagers\StudentSubjectResultsRelationManager;
use App\Models\Academic\StudentEnrollment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentEnrollmentResource extends Resource
{
    protected static ?string $model = StudentEnrollment::class;

    protected static ?string $navigationLabel = 'Student Enrollments';

    protected static \UnitEnum|string|null $navigationGroup = 'Student Management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return StudentEnrollmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentEnrollmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudentSubjectResultsRelationManager::class,
            MarksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentEnrollments::route('/'),
            'create' => CreateStudentEnrollment::route('/create'),
            'view' => ViewStudentEnrollment::route('/{record}'),
            'edit' => EditStudentEnrollment::route('/{record}/edit'),
        ];
    }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record?->student?->full_name ?? '';
    }
}
