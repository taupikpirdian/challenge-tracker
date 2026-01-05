<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChallengeResource\Pages;
use App\Filament\Resources\ChallengeResource\RelationManagers;
use App\Models\Challenge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ChallengeResource extends Resource
{
    protected static ?string $model = Challenge::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Challenges';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Challenge Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Cover Image')
                            ->disk('s3')
                            ->directory('challenge-covers')
                            ->maxSize(10240) // 10MB
                            ->columnSpanFull()
                            ->helperText('Upload a cover image for the challenge (Max 10MB)')
                            ->image(),
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('s3')
                            ->fileAttachmentsDirectory('richeditor-uploads')
                            ->required(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->native(false)
                            ->after('start_date'),
                        Forms\Components\TextInput::make('duration_days')
                            ->required()
                            ->numeric()
                            ->default(30)
                            ->minValue(1)
                            ->maxValue(365),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft')
                            ->searchable(),
                        Forms\Components\Hidden::make('created_by')
                            ->default(Auth::id()),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Form Builder Configuration')
                    ->description('Define the form fields that users will fill when submitting their progress')
                    ->schema([
                        Forms\Components\Repeater::make('rules')
                            ->relationship()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull()
                                            ->helperText('The field label that users will see (e.g., "Weight", "Distance", "Notes")'),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
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
                                                Forms\Components\TextInput::make('order_number')
                                                    ->required()
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(1)
                                                    ->helperText('The order in which this field appears in the form'),
                                            ]),
                                        Forms\Components\Toggle::make('is_required')
                                            ->default(false)
                                            ->inline(false)
                                            ->columnSpanFull()
                                            ->helperText('Whether this field must be filled by the user'),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(1)
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->collapsed()
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->addActionLabel('Add Rule'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_url')
                    ->label('Cover')
                    ->circular(false)
                    ->defaultImageUrl(url('/images/placeholder-challenge.png'))
                    ->size(80),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->sortable()
                    ->suffix(' days'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('creator.name')
                    ->sortable()
                    ->toggleable(),
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
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('participants')
                    ->label('Participants')
                    ->icon('heroicon-o-users')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.challenges.participants', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No challenges found')
            ->emptyStateDescription('Create your first challenge to get started.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RulesRelationManager::class,
            RelationManagers\SubmissionsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return parent::getEloquentQuery()
            ->when(!$user?->hasRole(['super admin', 'admin']), function (Builder $query) use ($user) {
                // Only show challenges created by the current user for non-admin roles
                $query->where('created_by', $user->id);
            });
    }

    public static function canViewAny(): bool
    {
        // All authenticated users can view challenges
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        // All authenticated users can create challenges
        return auth()->check();
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        // Admin and super admin can edit any challenge
        if ($user?->hasRole(['super admin', 'admin'])) {
            return true;
        }

        // Users can only edit their own challenges
        return $record->created_by === $user?->id;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();

        // Admin and super admin can delete any challenge
        if ($user?->hasRole(['super admin', 'admin'])) {
            return true;
        }

        // Users can only delete their own challenges
        return $record->created_by === $user?->id;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->check();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChallenges::route('/'),
            'create' => Pages\CreateChallenge::route('/create'),
            'view' => Pages\ViewChallenge::route('/{record}'),
            'edit' => Pages\EditChallenge::route('/{record}/edit'),
            'participants' => Pages\ManageParticipants::route('/{record}/participants'),
            'participant-submissions' => Pages\ViewParticipantSubmissions::route('/{record}/participants/{participant}'),
        ];
    }
}
