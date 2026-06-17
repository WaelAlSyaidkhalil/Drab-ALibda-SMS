<x-filament-panels::page>
    <form wire:submit="save" class="flex flex-col gap-6">
        {{ $this->form }}

        <x-filament::button type="submit" style="margin-top: 40px; display: block; width: 10%;">
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>