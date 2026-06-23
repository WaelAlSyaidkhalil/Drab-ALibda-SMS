<?php

namespace App\Filament\Resources\Attendances;

use App\Filament\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Resources\Attendances\Schemas\AttendanceForm;
use App\Filament\Resources\Attendances\Tables\AttendancesTable;
use App\Models\Schedule\Attendance;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Override;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static bool $shouldRegisterNavigation = false;
    /**
     * 🔥 IMPORTANT: this enables calendar filtering
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(request('date'), fn ($query) =>
                $query->whereDate('date', request('date'))
            )
            ->when(request('school_class_id'), fn ($query) =>
                $query->whereHas('schedule', fn ($schedule) =>
                    $schedule->where('school_class_id', request('school_class_id'))
                )
            )
            ->when(request('section_id'), fn ($query) =>
                $query->whereHas('schedule', fn ($schedule) =>
                    $schedule->where('section_id', request('section_id'))
                )
            );
    }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record?->student?->first_name ?? 'Unknown Student';
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
        ];
    }
}