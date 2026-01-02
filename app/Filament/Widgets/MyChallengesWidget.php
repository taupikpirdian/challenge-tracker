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
            ->withCount(['submissions as pending_submissions_count' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->get()
            ->map(function ($challenge) {
                // Ensure slug exists, if not generate it
                if (!$challenge->slug) {
                    $challenge->slug = \Illuminate\Support\Str::slug($challenge->title);
                }
                return $challenge;
            });

        // Count total pending submissions for challenges created by the user
        $totalPendingSubmissions = Challenge::where('created_by', $user->id)
            ->whereHas('submissions', function ($query) {
                $query->where('status', 'pending');
            })
            ->withCount(['submissions as pending_count' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->get()
            ->sum('pending_count');

        return [
            'challenges' => $myChallenges,
            'totalPendingSubmissions' => $totalPendingSubmissions,
        ];
    }

    public static function canView(): bool
    {
        $user = Auth::user();

        // Only show for participants
        return $user && $user->hasRole(['participant']);
    }
}
