<?php

namespace App\Filament\Resources;

use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveRequestType;
use App\Filament\Resources\EmployeeResource\RelationManagers\LeaveRequestsRelationManager;
use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Filament\Resources\LeaveRequestResource\RelationManagers;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::getFormFields());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::getTableColumns())
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }

    public static function getFormFields(): array
    {
        return [
            Forms\Components\Select::make('employee_id')
                ->relationship('employee', 'name')
                ->preload()
                ->searchable()
                ->hiddenOn(LeaveRequestsRelationManager::class) // hide employee id when shown in relation
                ->required(),
            Forms\Components\Fieldset::make('Start Ending')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('start_date')
                        ->native(false)
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->native(false)
                        ->required(),
                ]),
            Forms\Components\Select::make('type')
                ->enum(LeaveRequestType::class)
                ->options(LeaveRequestType::class)
                ->required(),
            Forms\Components\Select::make('status')
                ->enum(LeaveRequestStatus::class)
                ->options(LeaveRequestStatus::class)
                ->required(),
            Forms\Components\MarkdownEditor::make('reason')
                ->columnSpanFull(),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('employee.name')
                ->hiddenOn(LeaveRequestsRelationManager::class) // hide employee id when shown in relation
                ->sortable(),
            Tables\Columns\TextColumn::make('start_date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('end_date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('type')
                ->searchable(),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn($state) => $state->getColor()),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
