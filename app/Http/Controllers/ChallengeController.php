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
     * Calculate streak for a user's submissions (only approved submissions)
     */
    private function calculateStreak($userId, $challengeId): int
    {
        // Get all approved submissions for this user in this challenge, ordered by date
        $submissions = Submission::where('user_id', $userId)
            ->where('challenge_id', $challengeId)
            ->where('status', 'approved')
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

        // Get top 10 participants for leaderboard (only approved submissions)
        $topParticipants = Submission::where('challenge_id', $challenge->id)
            ->where('status', 'approved')
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
                    // Calculate streak for this participant (only approved submissions)
                    $participant->streak = $this->calculateStreak($item->user_id, $challenge->id);
                }
                return $participant;
            })
            ->filter()
            ->values();

        // Calculate challenge statistics
        $allParticipants = ChallengeParticipant::where('challenge_id', $challenge->id)->get();
        $activeStreakCount = 0;
        $leftBehindCount = 0;

        foreach ($allParticipants as $participant) {
            $streak = $this->calculateStreak($participant->user_id, $challenge->id);
            if ($streak > 0) {
                $activeStreakCount++;
            } else {
                // Check if they have any approved submissions
                $hasSubmissions = Submission::where('user_id', $participant->user_id)
                    ->where('challenge_id', $challenge->id)
                    ->where('status', 'approved')
                    ->exists();
                if ($hasSubmissions) {
                    $leftBehindCount++;
                }
            }
        }

        // New submissions in last 24 hours (only approved)
        $newSubmissionsCount = Submission::where('challenge_id', $challenge->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        // Total submissions (only approved)
        $totalSubmissions = Submission::where('challenge_id', $challenge->id)
            ->where('status', 'approved')
            ->count();

        // Get feed data - only approved submissions with their values and user info
        $feedSubmissions = Submission::where('challenge_id', $challenge->id)
            ->where('status', 'approved')
            ->with(['user', 'values.rule'])
            ->orderBy('submitted_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($submission) {
                // Add formatted time ago
                $submission->time_ago = $submission->submitted_at
                    ? $submission->submitted_at->diffForHumans()
                    : $submission->created_at->diffForHumans();
                return $submission;
            });

        return view('challenges.show', compact(
            'challenge',
            'isParticipant',
            'userSubmissions',
            'topParticipants',
            'activeStreakCount',
            'leftBehindCount',
            'newSubmissionsCount',
            'totalSubmissions',
            'feedSubmissions'
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

    /**
     * Display submission detail page (public for sharing).
     */
    public function showSubmissionDetail(string $slug, int $submission): View
    {
        $challenge = Challenge::where('slug', $slug)
            ->with(['creator'])
            ->firstOrFail();

        $submission = Submission::where('id', $submission)
            ->where('challenge_id', $challenge->id)
            ->with(['user', 'values.rule', 'values.files'])
            ->firstOrFail();

        // Get first image for OG tag
        $firstImage = null;
        if($submission->values && $submission->values->count() > 0) {
            foreach($submission->values as $value) {
                if($value->rule && ($value->rule->field_type === 'image' || $value->rule->field_type === 'file')) {
                    if($value->value_text) {
                        $filePath = $value->value_text;
                        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'])) {
                            $firstImage = \App\Helpers\MinioHelper::getProxyUrl($value->value_text);
                            break;
                        }
                    }
                }
            }
        }

        // Calculate progress stats
        $totalDays = $challenge->duration_days;
        $submittedDays = Submission::where('user_id', $submission->user_id)
            ->where('challenge_id', $challenge->id)
            ->where('status', 'approved')
            ->count();
        $progressPercentage = min(100, round(($submittedDays / $totalDays) * 100));

        // Calculate streak
        $currentStreak = $this->calculateStreak($submission->user_id, $challenge->id);

        return view('challenges.submission-detail', compact(
            'challenge',
            'submission',
            'firstImage',
            'totalDays',
            'submittedDays',
            'progressPercentage',
            'currentStreak'
        ));
    }
}
