<?php

namespace App\Filament\Resources\ChallengeResource\Pages;

use App\Filament\Resources\ChallengeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;

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
            Actions\Action::make('view_frontend')
                ->label('View on Frontend')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => route('challenges.show', $this->record->slug))
                ->openUrlInNewTab()
                ->color('gray'),
            Actions\EditAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Eager load relationships
        $this->record->load(['creator', 'rules', 'participants', 'submissions']);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Challenge Overview')
                    ->schema([
                        ImageEntry::make('cover_image')
                            ->label('Cover Image')
                            ->height(300)
                            ->defaultImageUrl(url('/images/placeholder-challenge.png'))
                            ->columnSpanFull(),
                        TextEntry::make('title')
                            ->size('text-2xl')
                            ->weight('bold')
                            ->columnSpanFull(),
                        TextEntry::make('description')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Challenge Details')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'active' => 'success',
                                'completed' => 'primary',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('start_date')
                            ->date()
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('end_date')
                            ->date()
                            ->icon('heroicon-o-calendar-days'),
                        TextEntry::make('duration_days')
                            ->suffix(' days')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('creator.name')
                            ->label('Created By')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(3),

                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('participants_count')
                            ->label('Total Participants')
                            ->state(fn ($record) => $record->participants()->count())
                            ->icon('heroicon-o-users')
                            ->color('primary'),
                        TextEntry::make('rules_count')
                            ->label('Total Rules')
                            ->state(fn ($record) => $record->rules()->count())
                            ->icon('heroicon-o-list-bullet')
                            ->color('success'),
                        TextEntry::make('submissions_count')
                            ->label('Total Submissions')
                            ->state(fn ($record) => $record->submissions()->count())
                            ->icon('heroicon-o-document-text')
                            ->color('warning'),
                    ])
                    ->columns(3),
            ]);
    }
}

