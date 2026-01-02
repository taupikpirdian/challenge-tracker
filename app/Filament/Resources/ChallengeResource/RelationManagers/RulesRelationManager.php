<?php

namespace App\Filament\Resources\ChallengeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RulesRelationManager extends RelationManager
{
    protected static string $relationship = 'rules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Builder Configuration')
                    ->description('Define the form fields that users will fill when submitting their progress')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->required()
                            ->maxLength(255)
                            ->helperText('The field label that users will see (e.g., "Weight", "Distance", "Notes")'),
                        Forms\Components\Select::make('field_type')
                            ->required()
                            ->options([
                                'text' => 'Text Input',
                                'number' => 'Number Input',
                                'textarea' => 'Text Area',
                                'date' => 'Date Picker',
                                'time' => 'Time Picker',
                                'datetime' => 'Date & Time Picker',
                                'file' => 'File Upload',
                                'image' => 'Image Upload',
                                'select' => 'Dropdown Select',
                                'radio' => 'Radio Button',
                                'checkbox' => 'Checkbox',
                                'toggle' => 'Toggle Switch',
                            ])
                            ->default('text')
                            ->searchable()
                            ->helperText('The type of input field for this form element'),
                        Forms\Components\Toggle::make('is_required')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Whether this field must be filled by the user'),
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->helperText('The order in which this field appears in the form'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('field_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'number' => 'blue',
                        'textarea' => 'primary',
                        'date' => 'success',
                        'time' => 'warning',
                        'datetime' => 'danger',
                        'file' => 'purple',
                        'image' => 'pink',
                        'select' => 'info',
                        'radio' => 'cyan',
                        'checkbox' => 'indigo',
                        'toggle' => 'orange',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_required')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->defaultSort('order_number')
            ->reorderable('order_number')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
