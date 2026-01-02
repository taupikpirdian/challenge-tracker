<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeRule;
use App\Models\Submission;
use App\Models\SubmissionValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    /**
     * Store a newly created submission in storage.
     */
    public function store(Request $request, Challenge $challenge): RedirectResponse
    {
        // Validate that user is a participant
        $participant = $challenge->participants()
            ->where('user_id', auth()->id())
            ->first();

        if (!$participant) {
            return redirect()->back()
                ->with('error', 'You must join this challenge before submitting progress.');
        }

        // Validate submission date
        $request->validate([
            'submission_date' => 'required|date|after_or_equal:'.$challenge->start_date->format('Y-m-d').'|before_or_equal:'.$challenge->end_date->format('Y-m-d'),
            'fields' => 'required|array',
        ], [
            'submission_date.after_or_equal' => 'The submission date must be within the challenge period.',
            'submission_date.before_or_equal' => 'The submission date must be within the challenge period.',
        ]);

        // Convert submission_date to a format we can use for uniqueness check
        $submissionDate = \Carbon\Carbon::parse($request->submission_date)->format('Y-m-d');

        // Check if submission already exists for this date
        // We'll store the date as day_number = timestamp of the date (to avoid schema changes)
        $dateTimestamp = \Carbon\Carbon::parse($submissionDate)->timestamp;

        $existingSubmission = Submission::where('challenge_id', $challenge->id)
            ->where('user_id', auth()->id())
            ->where('day_number', $dateTimestamp)
            ->first();

        if ($existingSubmission) {
            return redirect()->back()
                ->with('error', 'You have already submitted progress for ' . \Carbon\Carbon::parse($submissionDate)->format('M d, Y'));
        }

        DB::beginTransaction();

        try {
            // Create submission
            $submission = Submission::create([
                'user_id' => auth()->id(),
                'challenge_id' => $challenge->id,
                'day_number' => $dateTimestamp, // Store timestamp as day_number
                'submitted_at' => now(),
                'status' => 'pending',
            ]);

            // Process form fields
            foreach ($request->fields as $ruleId => $value) {
                $rule = ChallengeRule::find($ruleId);

                if (!$rule || $rule->challenge_id !== $challenge->id) {
                    continue;
                }

                $submissionValue = new SubmissionValue();
                $submissionValue->submission_id = $submission->id;
                $submissionValue->rule_id = $ruleId;

                // Store value based on field type
                switch ($rule->field_type) {
                    case 'text':
                    case 'textarea':
                        $submissionValue->value_text = $value;
                        break;
                    case 'number':
                        $submissionValue->value_number = $value;
                        break;
                    case 'date':
                    case 'time':
                    case 'datetime':
                        $submissionValue->value_text = $value;
                        break;
                    case 'checkbox':
                    case 'toggle':
                        $submissionValue->value_boolean = (bool) $value;
                        break;
                    case 'radio':
                        $submissionValue->value_text = $value;
                        break;
                    case 'file':
                    case 'image':
                        // Check if file was uploaded
                        $fileKey = "fields.$ruleId";
                        if ($request->hasFile($fileKey)) {
                            $file = $request->file($fileKey);
                            if ($file && $file->isValid()) {
                                $path = $file->store('submissions', 'public');
                                $submissionValue->value_text = $path;
                            }
                        }
                        break;
                    default:
                        $submissionValue->value_text = $value;
                }

                $submissionValue->save();
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Progress submitted successfully for ' . \Carbon\Carbon::parse($submissionDate)->format('M d, Y') . '!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to submit progress: ' . $e->getMessage());
        }
    }
}
