<?php

namespace App\Filament\Resources\Terms;

use App\Filament\Resources\Terms\Pages\ManageTerms;
use App\Models\Subjects\Term;
use App\Enums\TermType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static \UnitEnum|string|null $navigationGroup = 'School Management';

    protected static ?string $navigationLabel = 'Terms';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(collect(TermType::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])->toArray())
                    ->required(),
                TextInput::make('academic_year'),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('term_name')
                    ->sortable(),

                TextColumn::make('academic_year')
                    ->sortable(),

                TextColumn::make('duration'),

                TextColumn::make('days_remaining')
                    ->state(fn (Term $record) => $record->getDaysRemaining())
                    ->badge()
                    ->color(fn ($state) => $state > 30 ? 'success' : ($state > 0 ? 'warning' : 'danger')),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'نشط' => 'success',
                        'قادم' => 'warning',
                        'مكتمل' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('academic_year')
                    ->options(
                        Term::query()
                            ->select('academic_year')
                            ->distinct()
                            ->pluck('academic_year', 'academic_year')
                            ->toArray()
                    ),
                Filter::make('status')
                    ->form([
                        Select::make('status')
                            ->options([
                                'active' => 'نشط',
                                'upcoming' => 'قادم',
                                'completed' => 'مكتمل',
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['status'] ?? null, function ($q, $status) {

                            return match ($status) {
                                'active' => $q->where('start_date', '<=', now())
                                            ->where('end_date', '>=', now()),

                                'upcoming' => $q->where('start_date', '>', now()),

                                'completed' => $q->where('end_date', '<', now()),

                                default => $q,
                            };
                        });
                    })
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => ManageTerms::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
