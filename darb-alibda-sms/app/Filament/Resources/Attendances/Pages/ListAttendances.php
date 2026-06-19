<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getTitle(): string
{
    $date = request('date');
    $classId = request('class_id');
    $sectionId = request('section_id');

    $class = $classId
        ? \App\Models\Academic\SchoolClass::find($classId)?->type?->label()
        : null;

    $section = $sectionId
        ? \App\Models\Academic\Section::find($sectionId)?->name
        : null;

    $parts = [];

    if ($date) {
        $parts[] = " {$date}";
    }

    if ($class) {
        $parts[] = " {$class}";
    }

    if ($section) {
        $parts[] = " {$section}";
    }

    return $parts
        ? implode('  |  ', $parts)
        : 'Students Attendance';
}
}
