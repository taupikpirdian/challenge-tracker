<?php

namespace App\Filament\Resources\ChallengeResource\Pages;

use App\Filament\Resources\ChallengeResource;
use App\Filament\Resources\SubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

class ViewParticipantSubmissions extends Page implements HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    protected static string $resource = ChallengeResource::class;

    protected static string $view = 'filament.resources.challenge-resource.pages.view-participant-submissions';

    protected static bool $isLazyLoaded = false;

    public $record = null;

    public $participant = null;

    public function mount($record, $participant): void
    {
        // Load challenge
        $this->record = ChallengeResource::getModel()::findOrFail($record);

        // Load participant with relationships
        $this->participant = \App\Models\ChallengeParticipant::with(['user'])->findOrFail($participant);

        // Load submissions directly
        $submissions = \App\Models\Submission::where('challenge_id', $this->record->id)
            ->where('user_id', $this->participant->user_id)
            ->get();

        // Attach submissions to participant
        $this->participant->setRelation('submissions', $submissions);

        // Abort if participant doesn't belong to this challenge
        if ($this->participant->challenge_id !== $this->record->id) {
            abort(404);
        }

        // Check authorization
        $user = auth()->user();

        if (!$user?->hasRole(['super admin', 'admin']) && $this->record->created_by !== $user?->id) {
            abort(403);
        }
    }

    public function getBreadcrumb(): string
    {
        return 'Submissions';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_participants')
                ->label('Back to Participants')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => route('filament.admin.resources.challenges.participants', ['record' => $this->record])),
        ];
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Models\Submission::query()
            ->where('challenge_id', $this->record->id)
            ->where('user_id', $this->participant->user_id)
            ->with(['values']);
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('formatted_date')
                ->label('Submission Date')
                ->date('M d, Y')
                ->sortable(),
            \Filament\Tables\Columns\TextColumn::make('submitted_at')
                ->label('Submitted At')
                ->dateTime('M d, Y H:i')
                ->sortable(),
            \Filament\Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'gray',
                }),
            \Filament\Tables\Columns\TextColumn::make('created_at')
                ->label('Created')
                ->dateTime('M d, Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            \Filament\Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            \Filament\Tables\Actions\ViewAction::make()
                ->url(fn ($record): string => route('filament.admin.resources.submissions.view', ['record' => $record])),
        ];
    }

    public function getViewData(): array
    {
        $submissions = $this->participant->submissions;

        return [
            'participant' => $this->participant,
            'challenge' => $this->record,
            'totalSubmissions' => $submissions->count(),
            'approvedSubmissions' => $submissions->where('status', 'approved')->count(),
            'pendingSubmissions' => $submissions->where('status', 'pending')->count(),
            'rejectedSubmissions' => $submissions->where('status', 'rejected')->count(),
        ];
    }
}
