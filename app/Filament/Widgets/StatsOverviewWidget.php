<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Challenge;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        // Only show for admin and super admin
        return $user && $user->hasRole(['super admin', 'admin']);
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Challenges', Challenge::count())
                ->description('All challenges in the system')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary'),

            Stat::make('Total Participants', User::whereHas('roles', function($query) {
                $query->where('name', 'participant');
            })->count())
                ->description('Registered participants')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('Submissions Today', Submission::whereDate('submitted_at', today())->count())
                ->description('Submitted today')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('warning'),

            Stat::make('Pending Validations', Submission::where('status', 'pending')->count())
                ->description('Awaiting validation')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
