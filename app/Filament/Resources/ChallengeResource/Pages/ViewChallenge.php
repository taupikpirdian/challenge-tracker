<?php

namespace App\Filament\Resources\ChallengeResource\Pages;

use App\Filament\Resources\ChallengeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewChallenge extends ViewRecord
{
    protected static string $resource = ChallengeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('participants')
                ->label('Manage Participants')
                ->icon('heroicon-o-users')
                ->color('primary')
                ->url(fn (): string => route('filament.admin.resources.challenges.participants', ['record' => $this->record])),
            Actions\EditAction::make(),
        ];
    }
}
