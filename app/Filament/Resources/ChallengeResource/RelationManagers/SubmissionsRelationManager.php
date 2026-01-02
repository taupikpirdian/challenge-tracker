<?php

namespace App\Filament\Resources\ChallengeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'Submissions';

    protected static ?string $modelLabel = 'Submission';

    protected static ?string $pluralModelLabel = 'Submissions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Submission Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('day_number')
                            ->label('Submission Date')
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                return \Carbon\Carbon::createFromTimestamp($state)->format('M d, Y');
                            }),
                        Forms\Components\DateTimePicker::make('submitted_at')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Submission Data')
                    ->schema([
                        Forms\Components\ViewField::make('submission_values')
                            ->label('Submitted Fields')
                            ->view('filament.components.submission-values-display')
                            ->viewData(function ($record) {
                                return [
                                    'submission' => $record,
                                ];
                            }),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Approval Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('pending')
                            ->live(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Participant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('formatted_date')
                    ->label('Submission Date')
                    ->sortable()
                    ->date('M d, Y'),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Tables\Contracts\HasTable $livewire) {
                            $records = $livewire->getSelectedTableRecords();
                            foreach ($records as $record) {
                                $record->update(['status' => 'approved']);
                            }
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Tables\Contracts\HasTable $livewire) {
                            $records = $livewire->getSelectedTableRecords();
                            foreach ($records as $record) {
                                $record->update(['status' => 'rejected']);
                            }
                        }),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc');
    }
}
