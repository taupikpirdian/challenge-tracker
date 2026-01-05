<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\Submission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Submissions';

    protected static ?string $modelLabel = 'Submission';

    protected static ?string $pluralModelLabel = 'Submissions';

    protected static ?int $navigationSort = 3;

    protected static bool $isScopedToTenant = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Submission Information')
                    ->schema([
                        Forms\Components\Select::make('challenge_id')
                            ->label('Challenge')
                            ->options(function () {
                                $user = auth()->user();
                                if (!$user) {
                                    return [];
                                }

                                // If admin or super admin, show all challenges
                                if ($user->hasRole(['super admin', 'admin'])) {
                                    return \App\Models\Challenge::query()
                                        ->where('status', 'active')
                                        ->orderBy('title')
                                        ->pluck('title', 'id')
                                        ->toArray();
                                }

                                // Otherwise, only show challenges created by the user
                                return \App\Models\Challenge::query()
                                    ->where('status', 'active')
                                    ->where('created_by', $user->id)
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                // When challenge is selected, reset user selection
                                if ($state) {
                                    $set('user_id', null);
                                }
                            }),
                        Forms\Components\Select::make('user_id')
                            ->label('Participant')
                            ->placeholder(function (Forms\Get $get) {
                                $challengeId = $get('challenge_id');
                                if (!$challengeId) {
                                    return 'Please select a challenge first';
                                }

                                $participantCount = \App\Models\ChallengeParticipant::where('challenge_id', $challengeId)->count();
                                return "Search participant ({$participantCount} participants in this challenge)";
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Forms\Get $get) => !$get('challenge_id'))
                            ->getSearchResultsUsing(function (Forms\Get $get, string $search) {
                                $challengeId = $get('challenge_id');
                                if (!$challengeId) {
                                    return [];
                                }

                                // Get user IDs from ChallengeParticipant
                                $participantIds = \App\Models\ChallengeParticipant::where('challenge_id', $challengeId)
                                    ->pluck('user_id')
                                    ->toArray();

                                if (empty($participantIds)) {
                                    return [];
                                }

                                return \App\Models\User::query()
                                    ->whereIn('id', $participantIds)
                                    ->where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->options(function (Forms\Get $get) {
                                // Preload all participants when challenge is selected
                                $challengeId = $get('challenge_id');
                                if (!$challengeId) {
                                    return [];
                                }

                                $participantIds = \App\Models\ChallengeParticipant::where('challenge_id', $challengeId)
                                    ->pluck('user_id')
                                    ->toArray();

                                if (empty($participantIds)) {
                                    return [];
                                }

                                return \App\Models\User::query()
                                    ->whereIn('id', $participantIds)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): string {
                                $user = \App\Models\User::find($value);
                                return $user ? $user->name : $value;
                            })
                            ->helperText(function (Forms\Get $get) {
                                $challengeId = $get('challenge_id');
                                if (!$challengeId) {
                                    return 'Select a challenge above to see participants';
                                }

                                $participantCount = \App\Models\ChallengeParticipant::where('challenge_id', $challengeId)->count();
                                return "This challenge has {$participantCount} participant(s). Search to select one.";
                            }),
                        Forms\Components\DatePicker::make('submission_date')
                            ->label('Submission Date')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->rules(function (Forms\Get $get) {
                                return [
                                    'required',
                                    function (string $attribute, $value, callable $fail) use ($get) {
                                        $challengeId = $get('challenge_id');
                                        $userId = $get('user_id');

                                        if (!$challengeId || !$userId || !$value) {
                                            return;
                                        }

                                        // Convert date to timestamp
                                        $dayNumber = \Carbon\Carbon::parse($value)->timestamp;

                                        // Check if submission already exists
                                        $exists = \App\Models\Submission::where('user_id', $userId)
                                            ->where('challenge_id', $challengeId)
                                            ->where('day_number', $dayNumber)
                                            ->exists();

                                        if ($exists) {
                                            $formattedDate = \Carbon\Carbon::parse($value)->format('M d, Y');
                                            $fail("A submission already exists for this participant on {$formattedDate}. Please choose a different date.");
                                        }
                                    },
                                ];
                            }),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Submission Data')
                    ->schema([
                        Forms\Components\Placeholder::make('note')
                            ->label('Note')
                            ->content('Select a challenge first to load the submission form fields.')
                            ->visible(fn (Forms\Get $get) => !$get('challenge_id')),
                        Forms\Components\Grid::make(1)
                            ->schema(function (Forms\Get $get) {
                                $challengeId = $get('challenge_id');
                                if (!$challengeId) {
                                    return [];
                                }

                                // Load challenge with rules for better performance
                                $challenge = \App\Models\Challenge::with('rules')->find($challengeId);
                                if (!$challenge || !$challenge->rules || $challenge->rules->isEmpty()) {
                                    return [
                                        Forms\Components\Placeholder::make('no_rules')
                                            ->label('No Form Fields')
                                            ->content('This challenge does not have any form fields configured.')
                                    ];
                                }

                                $fields = [];
                                foreach ($challenge->rules->sortBy('order_number') as $rule) {
                                    $field = null;

                                    switch ($rule->field_type) {
                                        case 'text':
                                            $field = Forms\Components\TextInput::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->placeholder('Enter ' . $rule->label);
                                            break;

                                        case 'number':
                                            $field = Forms\Components\TextInput::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->numeric()
                                                ->placeholder('Enter ' . $rule->label);
                                            break;

                                        case 'textarea':
                                            $field = Forms\Components\Textarea::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->rows(3)
                                                ->placeholder('Enter ' . $rule->label);
                                            break;

                                        case 'date':
                                            $field = Forms\Components\DatePicker::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->native(false);
                                            break;

                                        case 'time':
                                            $field = Forms\Components\TimePicker::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->native(false);
                                            break;

                                        case 'datetime':
                                            $field = Forms\Components\DateTimePicker::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->native(false);
                                            break;

                                        case 'file':
                                            $field = Forms\Components\FileUpload::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->directory('submissions')
                                                ->maxSize(5120);
                                            break;

                                        case 'image':
                                            $field = Forms\Components\FileUpload::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->directory('submissions')
                                                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                                                ->image()
                                                ->maxSize(5120);
                                            break;

                                        case 'select':
                                            $field = Forms\Components\Select::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->options($rule->options ? json_decode($rule->options, true) : [])
                                                ->searchable();
                                            break;

                                        case 'radio':
                                            $field = Forms\Components\Radio::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->options($rule->options ? json_decode($rule->options, true) : []);
                                            break;

                                        case 'checkbox':
                                        case 'toggle':
                                            $field = Forms\Components\Toggle::make('fields.' . $rule->id)
                                                ->label($rule->label)
                                                ->required($rule->is_required)
                                                ->inline(false);
                                            break;
                                    }

                                    if ($field) {
                                        $fields[] = $field;
                                    }
                                }

                                return $fields;
                            })
                            ->visible(fn (Forms\Get $get) => $get('challenge_id')),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Submission Status')
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
                    ])
                    ->visible(fn ($record) => $record && $record->exists),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Participant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('challenge.title')
                    ->label('Challenge')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['values', 'user', 'challenge']);
            })
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('challenge')
                    ->relationship('challenge', 'title'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Details')
                    ->modalHeading(fn ($record) => "Submission Details - {$record->user->name}")
                    ->modalDescription(fn ($record) => "Challenge: {$record->challenge->title}")
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->color('primary'),
                Tables\Actions\EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        $query = parent::getEloquentQuery();

        // If not admin/super admin, only show submissions for challenges created by the user
        if (!$user || !$user->hasRole(['super admin', 'admin'])) {
            $query->whereHas('challenge', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            });
        }

        return $query;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Submission Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Participant'),
                        Infolists\Components\TextEntry::make('challenge.title')
                            ->label('Challenge'),
                        Infolists\Components\TextEntry::make('formatted_date')
                            ->label('Submission Date')
                            ->date('M d, Y'),
                        Infolists\Components\TextEntry::make('submitted_at')
                            ->label('Submitted At')
                            ->dateTime('M d, Y H:i:s'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Submission Data')
                    ->schema([
                        Infolists\Components\Grid::make(1)
                            ->schema(function ($record) {
                                if (!$record || !$record->challenge) {
                                    return [];
                                }

                                // Load challenge with rules
                                $challenge = \App\Models\Challenge::with('rules')->find($record->challenge_id);
                                if (!$challenge || !$challenge->rules || $challenge->rules->isEmpty()) {
                                    return [
                                        Infolists\Components\TextEntry::make('no_data')
                                            ->label('No Data')
                                            ->default('No form fields configured for this challenge.')
                                    ];
                                }

                                $entries = [];

                                foreach ($challenge->rules->sortBy('order_number') as $rule) {
                                    // Find the submission value for this rule
                                    $submissionValue = $record->values->where('rule_id', $rule->id)->first();

                                    $value = null;
                                    if ($submissionValue) {
                                        // Get value based on field type
                                        switch ($rule->field_type) {
                                            case 'text':
                                            case 'textarea':
                                            case 'radio':
                                            case 'select':
                                            case 'date':
                                            case 'time':
                                            case 'datetime':
                                            case 'file':
                                            case 'image':
                                                $value = $submissionValue->value_text;
                                                break;
                                            case 'number':
                                                $value = $submissionValue->value_number;
                                                break;
                                            case 'checkbox':
                                            case 'toggle':
                                                $value = $submissionValue->value_boolean;
                                                break;
                                        }
                                    }

                                    // Create entry based on field type
                                    switch ($rule->field_type) {
                                        case 'image':
                                            $entry = Infolists\Components\ImageEntry::make('value')
                                                ->label($rule->label)
                                                ->default(fn () => !empty($value) ? \App\Helpers\MinioHelper::getProxyUrl($value) : null)
                                                ->visible(fn () => !empty($value));
                                            break;

                                        case 'file':
                                            // Check if it's an image file
                                            $isImage = !empty($value) && in_array(strtolower(pathinfo($value, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);

                                            if ($isImage) {
                                                $entry = Infolists\Components\ImageEntry::make('value')
                                                    ->label($rule->label)
                                                    ->default(fn () => \App\Helpers\MinioHelper::getProxyUrl($value))
                                                    ->visible(fn () => !empty($value));
                                            } else {
                                                $entry = Infolists\Components\TextEntry::make('value')
                                                    ->label($rule->label)
                                                    ->url(fn () => !empty($value) ? \App\Helpers\MinioHelper::getProxyUrl($value) : null)
                                                    ->openUrlInNewTab()
                                                    ->default(fn () => !empty($value) ? basename($value) : '-')
                                                    ->formatStateUsing(fn ($state) => !empty($value) ? basename($value) : '-');
                                            }
                                            break;

                                        case 'checkbox':
                                        case 'toggle':
                                            $entry = Infolists\Components\IconEntry::make('value')
                                                ->label($rule->label)
                                                ->boolean()
                                                ->default($value);
                                            break;

                                        case 'number':
                                            $entry = Infolists\Components\TextEntry::make('value')
                                                ->label($rule->label)
                                                ->default(fn () => $value !== null ? number_format($value, 2) : '-');
                                            break;

                                        case 'textarea':
                                            $entry = Infolists\Components\TextEntry::make('value')
                                                ->label($rule->label)
                                                ->default(fn () => $value ?: '-')
                                                ->columnSpanFull();
                                            break;

                                        default:
                                            $entry = Infolists\Components\TextEntry::make('value')
                                                ->label($rule->label)
                                                ->default(fn () => $value ?: '-');
                                    }

                                    if ($entry) {
                                        $entries[] = $entry;
                                    }
                                }

                                return $entries;
                            })
                    ])
                    ->columns(1),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'view' => Pages\ViewSubmission::route('/{record}'),
            'edit' => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        // Allow users to create submissions for their own challenges' participants
        return auth()->check();
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }
}
