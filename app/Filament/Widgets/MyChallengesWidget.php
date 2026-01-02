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

        // Get challenges that the user is participating in OR created by the user, ONLY active status
        $myChallenges = Challenge::where('status', 'active')
            ->where(function ($query) use ($user) {
                // Challenges the user is participating in
                $query->whereHas('participants', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
                // OR challenges created by the user
                $query->orWhere('created_by', $user->id);
            })
            ->withCount('participants')
            ->get()
            ->map(function ($challenge) {
                // Ensure slug exists, if not generate it
                if (!$challenge->slug) {
                    $challenge->slug = \Illuminate\Support\Str::slug($challenge->title);
                }
                return $challenge;
            });

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
