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
