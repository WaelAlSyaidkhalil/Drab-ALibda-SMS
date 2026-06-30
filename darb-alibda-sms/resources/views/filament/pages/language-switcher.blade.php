<x-filament::dropdown placement="bottom-end" style="margin: 15px">
    <x-slot name="trigger">
        <x-filament::button
            color="gray"
            size="sm"
            icon="heroicon-o-language"
        >
            {{ strtoupper(app()->getLocale()) }}
        </x-filament::button>
    </x-slot>

    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            :href="route('locale.switch', ['locale' => 'en'])"
            tag="a"
        >
            English
        </x-filament::dropdown.list.item>

        <x-filament::dropdown.list.item
            :href="route('locale.switch', ['locale' => 'ar'])"
            tag="a"
        >
            العربية
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>