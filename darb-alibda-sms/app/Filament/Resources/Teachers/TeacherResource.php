<?php

namespace App\Filament\Resources\Teachers;

use App\Enums\Gender;
use App\Filament\Resources\Teachers\Pages\ManageTeachers;
use App\Models\Academic\Teacher;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Teacher Management';

    protected static ?string $navigationLabel = 'Teachers';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required(),

                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required(),

                TextInput::make('employee_number')
                    ->label('Employee Number')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('registry_number')
                    ->label('Registry Number'),

                TextInput::make('national_id')
                    ->label('National ID'),

                TextInput::make('specialization')
                    ->label('Specialization'),

                DatePicker::make('hire_date')
                    ->label('Hire Date'),

                TextInput::make('employment_type')
                    ->label('Employment Type'),

                TextInput::make('grade')
                    ->label('Grade'),

                TextInput::make('phone_alt')
                    ->label('Alternative Phone')
                    ->tel(),

                TextInput::make('experience_years')
                    ->label('Experience Years')
                    ->numeric()
                    ->minValue(0),

                Textarea::make('address')
                    ->label('Address')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Teacher')
                    ->searchable([
                        'first_name',
                        'last_name',
                    ])
                    ->sortable(),

                TextColumn::make('employee_number')
                    ->label('Employee No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('specialization')
                    ->label('Specialization')
                    ->searchable()
                    ->badge(),

                TextColumn::make('employment_type')
                    ->label('Employment Type')
                    ->toggleable(),

                TextColumn::make('grade')
                    ->label('Grade')
                    ->toggleable(),

                TextColumn::make('experience_years')
                    ->label('Experience')
                    ->suffix(' years')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('hire_date')
                    ->label('Hire Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('active')
                    ->query(fn (Builder $query) => $query->active()),
                SelectFilter::make('employment_type')
                    ->options(fn () => Teacher::query()->distinct()->pluck('employment_type', 'employment_type')->toArray())
                    ->label('Employment Type'),
                SelectFilter::make('specialization')
                    ->options(fn () => Teacher::query()->distinct()->pluck('specialization', 'specialization')->toArray())
                    ->label('Specialization'),
                SelectFilter::make('grade')
                    ->options(fn () => Teacher::query()->distinct()->pluck('grade', 'grade')->toArray())
                    ->label('Grade'),
                SelectFilter::make('experience_years')
                ->label('Experience Years')
                ->options([
                    '0-5' => '0-5 years',
                    '6-10' => '6-10 years',
                    '11-20' => '11-20 years',
                    '21+' => '21+ years',
                ])
                ->modifyQueryUsing(function (Builder $query, array $data): Builder {
                    return match ($data['value'] ?? null) {
                        '0-5' => $query->whereBetween('experience_years', [0, 5]),
                        '6-10' => $query->whereBetween('experience_years', [6, 10]),
                        '11-20' => $query->whereBetween('experience_years', [11, 20]),
                        '21+' => $query->where('experience_years', '>', 20),
                        default => $query,
                    };
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
            ])
            ->defaultSort('employee_number');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTeachers::route('/'),
        ];
    }
}
