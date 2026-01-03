<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    /**
     * Calculate streak for a user's submissions
     */
    private function calculateStreak($userId, $challengeId): int
    {
        // Get all submissions for this user in this challenge, ordered by date
        $submissions = Submission::where('user_id', $userId)
            ->where('challenge_id', $challengeId)
            ->orderBy('day_number', 'desc') // Get most recent first
            ->pluck('day_number')
            ->map(function($timestamp) {
                return \Carbon\Carbon::createFromTimestamp($timestamp)->startOfDay();
            })
            ->values();

        if ($submissions->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = now()->startOfDay();

        // Check if the most recent submission is from today or yesterday
        $mostRecent = $submissions->first();
        $daysDiff = $today->diffInDays($mostRecent);

        // If most recent submission is older than yesterday, streak is broken
        if ($daysDiff > 1) {
            return 0;
        }

        $streak = 1;
        $currentDate = $mostRecent;

        // Count consecutive days going backwards
        foreach ($submissions->skip(1) as $prevDate) {
            $daysDiff = $currentDate->diffInDays($prevDate);

            // If consecutive day (difference is exactly 1 day)
            if ($daysDiff === 1) {
                $streak++;
                $currentDate = $prevDate;
            } else {
                // Break the streak
                break;
            }
        }

        return $streak;
    }

    /**
     * Display the specified challenge.
     */
    public function show(string $slug): View
    {
        $challenge = Challenge::where('slug', $slug)
            ->with(['rules' => function ($query) {
                $query->orderBy('order_number');
            }, 'creator'])
            ->withCount('participants')
            ->firstOrFail();

        // Check if current user is a participant
        $isParticipant = false;
        $userSubmissions = collect();
        $topParticipants = collect();

        if (auth()->check()) {
            $isParticipant = ChallengeParticipant::where('user_id', auth()->id())
                ->where('challenge_id', $challenge->id)
                ->exists();

            // Get user's submission history for this challenge
            if ($isParticipant) {
                $userSubmissions = Submission::where('user_id', auth()->id())
                    ->where('challenge_id', $challenge->id)
                    ->with(['user', 'values.rule', 'values.files'])
                    ->orderBy('day_number', 'desc')
                    ->get();
            }
        }

        // Get top 10 participants for leaderboard
        $topParticipants = Submission::where('challenge_id', $challenge->id)
            ->selectRaw('user_id, COUNT(*) as submissions_count')
            ->groupBy('user_id')
            ->orderByDesc('submissions_count')
            ->limit(10)
            ->get()
            ->map(function($item) use ($challenge) {
                $participant = ChallengeParticipant::where('challenge_id', $challenge->id)
                    ->where('user_id', $item->user_id)
                    ->with('user')
                    ->first();
                if ($participant) {
                    $participant->submissions_count = $item->submissions_count;
                    // Calculate streak for this participant
                    $participant->streak = $this->calculateStreak($item->user_id, $challenge->id);
                }
                return $participant;
            })
            ->filter()
            ->values();

        return view('challenges.show', compact(
            'challenge',
            'isParticipant',
            'userSubmissions',
            'topParticipants'
        ));
    }

    /**
     * Join a challenge.
     */
    public function join(Challenge $challenge): RedirectResponse
    {
        // Check if user is already a participant
        $existingParticipant = ChallengeParticipant::where('user_id', auth()->id())
            ->where('challenge_id', $challenge->id)
            ->first();

        if ($existingParticipant) {
            return redirect()->back()
                ->with('info', 'You are already a participant of this challenge.');
        }

        // Check if challenge is active
        if ($challenge->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Cannot join a challenge that is not active.');
        }

        // Create participant record
        ChallengeParticipant::create([
            'user_id' => auth()->id(),
            'challenge_id' => $challenge->id,
            'joined_at' => now(),
            'status' => 'active',
        ]);

        return redirect()->back()
            ->with('success', 'Successfully joined the challenge! Good luck!');
    }

    /**
     * Leave a challenge.
     */
    public function leave(Challenge $challenge): RedirectResponse
    {
        $participant = ChallengeParticipant::where('user_id', auth()->id())
            ->where('challenge_id', $challenge->id)
            ->first();

        if (!$participant) {
            return redirect()->back()
                ->with('error', 'You are not a participant of this challenge.');
        }

        $participant->delete();

        return redirect()->back()
            ->with('success', 'You have left the challenge.');
    }
}
