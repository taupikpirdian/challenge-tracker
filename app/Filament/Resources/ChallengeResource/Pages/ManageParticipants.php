<?php

namespace App\Filament\Resources\ChallengeResource\Pages;

use App\Filament\Resources\ChallengeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManageParticipants extends ManageRelatedRecords
{
    protected static string $resource = ChallengeResource::class;

    protected static string $relationship = 'participants';

    protected static ?string $navigationLabel = 'Participants';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $breadcrumbTitle = 'Participants';

    protected static ?int $navigationSort = 2;

    public static function canAccess(array $arguments = []): bool
    {
        // Check if user can view this challenge's participants
        $challenge = $arguments['record'] ?? null;

        if (!$challenge instanceof Model) {
            return false;
        }

        $user = auth()->user();

        // Admin and super admin can view all
        if ($user?->hasRole(['super admin', 'admin'])) {
            return true;
        }

        // Users can only view participants of their own challenges
        return $challenge->created_by === $user?->id;
    }

    public function getBreadcrumb(): string
    {
        return 'Participants';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Participant')
                ->modalHeading('Add Participant to Challenge')
                ->createAnother(false)
                ->form([
                    \Filament\Forms\Components\Select::make('user_id')
                        ->label('User')
                        ->options(function () {
                            // Get users who are not already participants
                            $participantIds = $this->getRecord()->participants()->pluck('user_id')->toArray();

                            return \App\Models\User::query()
                                ->whereNotIn('id', $participantIds)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Search and select a user to add as participant'),
                ])
                ->successNotificationTitle('Participant added successfully'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Participant')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->user?->email ?? ''),
                Tables\Columns\TextColumn::make('joined_at')
                    ->label('Joined At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->getStateUsing(function ($record): int {
                        return \App\Models\Submission::where('challenge_id', $record->challenge_id)
                            ->where('user_id', $record->user_id)
                            ->count();
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_submissions')
                    ->label('View Submissions')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn ($record): string => route('filament.admin.resources.challenges.participant-submissions', [
                        'record' => $record->challenge_id,
                        'participant' => $record->id
                    ])),
                Tables\Actions\Action::make('view_user')
                    ->label('View User')
                    ->icon('heroicon-m-user')
                    ->color('gray')
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', ['record' => $record->user_id]))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->label('Remove')
                    ->requiresConfirmation()
                    ->modalHeading('Remove Participant')
                    ->modalDescription('Are you sure you want to remove this participant from the challenge?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Remove Participants')
                        ->modalDescription('Are you sure you want to remove these participants from the challenge?'),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['user']);
            })
            ->emptyStateHeading('No participants found')
            ->emptyStateDescription('Add participants to this challenge to get started.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
