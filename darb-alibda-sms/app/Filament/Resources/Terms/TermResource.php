<?php

namespace App\Filament\Resources\Terms;

use App\Enums\TermStatus;
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
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Validation\Rules\Unique;

class TermResource extends Resource
{
    protected static ?string $model = Term::class;

    protected static \UnitEnum|string|null $navigationGroup = 'School Management';

    protected static ?string $navigationLabel = 'Terms';

    public static function getNavigationGroup(): ?string
    {
        return __('dashboard.navigation.school_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.pages.terms');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('dashboard.labels.term'))
                    ->options(TermType::options())
                    ->required()
                    ->live()
                    ->validationMessages([
                        'required' => __('validation.custom.type.required'),
                    ]),
                TextInput::make('academic_year')
                    ->label(__('dashboard.labels.academic_year'))
                    ->regex('/^\d{4}-\d{4}$/')
                    ->unique(
                        table: 'terms',
                        column: 'academic_year',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                            ->where('type', $get('type'))
                    )
                    ->validationMessages([
                        'required' => __('validation.custom.academic_year.required'),
                        'regex' => __('dashboard.validation.academic_year_regex'),
                        'unique' => __('dashboard.validation.academic_year_unique'),
                    ]),
                DatePicker::make('start_date')
                    ->label(__('dashboard.labels.start_time'))
                    ->validationMessages([
                        'date' => __('validation.custom.start_date.date'),
                    ]),
                DatePicker::make('end_date')
                    ->label(__('dashboard.labels.end_time'))
                    ->validationMessages([
                        'date' => __('validation.custom.end_date.date'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('term_name')
                    ->label(__('dashboard.labels.name'))
                    ->sortable(query: function ($query, $direction) {
                        $query->orderByRaw("CASE type
                            WHEN 'First_Term' THEN 1
                            WHEN 'Second_Term' THEN 2
                            ELSE 99
                        END {$direction}");
                    }),

                TextColumn::make('academic_year')
                    ->label(__('dashboard.labels.academic_year'))
                    ->sortable(),

                TextColumn::make('duration')
                    ->label(__('dashboard.labels.duration')),

                TextColumn::make('days_remaining')
                    ->label(__('dashboard.labels.days_remaining'))
                    ->state(fn (Term $record) => $record->getDaysRemaining())
                    ->badge()
                    ->color(fn ($state) => $state > 30 ? 'success' : ($state > 0 ? 'warning' : 'danger')),

                TextColumn::make('status')
                    ->label(__('dashboard.labels.status'))
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge()
                    ->colors(TermStatus::getColors()),            ])
            ->filters([
                SelectFilter::make('academic_year')
                    ->label(__('dashboard.labels.academic_year'))
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
                            ->label(__('dashboard.labels.status'))
                            ->options(TermStatus::options()),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['status'] ?? null, function ($q, $status) {

                            return match ($status) {
                                TermStatus::ACTIVE => $q->where('start_date', '<=', now())
                                            ->where('end_date', '>=', now()),

                                TermStatus::UPCOMING => $q->where('start_date', '>', now()),

                                TermStatus::COMPLETED => $q->where('end_date', '<', now()),

                                default => $q,
                            };
                        });
                    })
            ])
            ->recordActions([
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

    public static function getPluralModelLabel(): string
    {
        return __('dashboard.labels.terms');
    }

    public static function getModelLabel(): string
    {
        return __('dashboard.pages.term');
    }
}
