<?php

namespace App\Filament\Resources\TimeSlots;

use App\Enums\TimeSlotNumber;
use App\Filament\Resources\TimeSlots\Pages\ManageTimeSlots;
use App\Models\Schedule\TimeSlot;
use App\Services\Admin\TimeSlotService;
use BackedEnum;
use Closure;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Utilities\Get as UtilitiesGet;

class TimeSlotResource extends Resource
{
    protected static ?string $model = TimeSlot::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Scheduling';

    protected static ?string $navigationLabel = 'Time Slots';

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.scheduling');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.time_slots');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TimePicker::make('start_time')
                ->label(__('dashboard.labels.start_time'))
                ->seconds(false)
                ->required(),

            TimePicker::make('end_time')
                ->label(__('dashboard.labels.end_time'))
                ->seconds(false)
                ->required()
                ->rule(function (UtilitiesGet $get) {
                    return function (
                        string $attribute,
                        mixed $value,
                        Closure $fail
                    ) use ($get) {
                        $startTime = $get('start_time');

                        if (! $startTime || ! $value) {
                            return;
                        }

                        if ($value <= $startTime) {
                            $fail(__('dashboard.validation.end_time_after_start'));

                            return;
                        }

                        if (
                            TimeSlotService::hasConflict(
                                $startTime,
                                $value
                            )
                        ) {
                            $fail(
                                __('dashboard.validation.overlapping_time_slot')
                            );
                        }
                    };
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('dashboard.labels.period'))
                    ->sortable(),

                TextColumn::make('display_time')
                    ->label(__('dashboard.labels.time_range'))
                    ->getStateUsing(
                        fn (TimeSlot $record) =>
                            $record->start_time->format('H:i') .
                            ' - ' .
                            $record->end_time->format('H:i')
                    ),

                TextColumn::make('duration_display')
                    ->label(__('dashboard.labels.duration')),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_time')
            ->actions([
                EditAction::make()->modalHeading(__('dashboard.buttons.edit') . ' ' . __('dashboard.pages.time_slot')),
                DeleteAction::make()->modalHeading(__('dashboard.buttons.delete') . ' ' . __('dashboard.pages.time_slot')),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTimeSlots::route('/'),
        ];
    }
}