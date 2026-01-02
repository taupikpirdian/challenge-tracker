<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Challenge;

class MyChallengesWidget extends Widget
{
    protected static string $view = 'filament.widgets.my-challenges-widget';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getViewData(): array
    {
        $user = Auth::user();

        // Get challenges that the user is participating in
        $myChallenges = Challenge::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with('participants')
        ->get();

        return [
            'challenges' => $myChallenges,
        ];
    }

    public static function canView(): bool
    {
        $user = Auth::user();

        // Only show for participants
        return $user && $user->hasRole(['participant']);
    }
}
