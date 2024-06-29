<?php

namespace App\Filament\Resources;

use App\Enums\EmployeeStatus;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Department;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationLabel = 'Karyawan';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Employee')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->prefixIcon('heroicon-o-user')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->prefixIcon('heroicon-o-envelope')
                        ->maxLength(255),
                    Forms\Components\Group::make([
                        Forms\Components\Select::make('department_id')
                            ->label('Department Name')
                            ->relationship('department', 'name')
                            ->options(Department::query()->whereActive(true)->get()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->editOptionForm(fn() => DepartmentResource::getFormFields())
                            ->required(),
                        Forms\Components\Select::make('position_id')
                            ->relationship('position', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn() => PositionResource::getFormFields())
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->enum(EmployeeStatus::class)
                            ->options(EmployeeStatus::class)
                            ->required(),
                    ])->columns(3)->columnSpan(3),
                    Forms\Components\DatePicker::make('joined')
                        ->prefixIcon('heroicon-o-calendar-days')
                        ->native(false)
                        ->default(now())
                        ->required(),
                ])
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('position:id,name');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('joined', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('department.name')
                    ->description(fn($record) => $record->position->name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->description(fn(Employee $employee) => $employee->email)
                    ->searchable(),
                Tables\Columns\TextColumn::make('joined')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(EmployeeStatus::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
