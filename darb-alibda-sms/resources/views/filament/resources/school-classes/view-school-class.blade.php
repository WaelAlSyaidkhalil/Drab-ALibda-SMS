<x-filament::page>
    <div class="space-y-4">
        {{-- Render Sections relation manager --}}
        @livewire(\App\Filament\Resources\SchoolClasses\RelationManagers\SectionsRelationManager::class, ['ownerRecord' => $record, 'pageClass' => \App\Filament\Resources\SchoolClasses\Pages\ViewSchoolClass::class])
    </div>
</x-filament::page>
