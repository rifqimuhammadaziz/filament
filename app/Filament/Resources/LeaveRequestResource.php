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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

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
            ->actions(
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->slideOver()
                        ->modalWidth('2xl'),
                    Tables\Actions\Action::make('approve')
                        ->requiresConfirmation() // modal for confirmation
                        ->visible(fn(LeaveRequest $record) => $record->status === LeaveRequestStatus::PENDING) // button only visible if pending
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(function (LeaveRequest $record) {
                            $record->approve();
                        })->after(function () {
                            Notification::make()
                                ->success()
                                ->title('Approved')
                                ->body('Leave request has been approved.')
                                ->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->color('danger')
                        ->requiresConfirmation() // modal for confirmation
                        ->visible(fn(LeaveRequest $record) => $record->status === LeaveRequestStatus::PENDING) // button only visible if pending
                        ->icon('heroicon-m-minus-circle')
                        ->action(fn(LeaveRequest $record) => $record->reject())
                        ->after(function () {
                            Notification::make()
                                ->danger()
                                ->title('Rejected')
                                ->body('Leave request has been rejected.')
                                ->send();
                        }),
                    Tables\Actions\Action::make('divider')
                        ->label('')
                        ->disabled(),
                    Tables\Actions\DeleteAction::make(),
                ])->color('gray')
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->color('success')
                        ->icon('heroicon-m-check-circle')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->approve())
                        ->after(function () {
                            Notification::make()
                                ->success()
                                ->title('Approved')
                                ->body('Leave request has been approved.')
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->color('danger')
                        ->icon('heroicon-m-minus-circle')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->reject())
                        ->after(function () {
                            Notification::make()
                                ->danger()
                                ->title('Rejected')
                                ->body('Leave request has been rejected.')
                                ->send();
                        })
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
            // 'create' => Pages\CreateLeaveRequest::route('/create'),
            // 'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
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
