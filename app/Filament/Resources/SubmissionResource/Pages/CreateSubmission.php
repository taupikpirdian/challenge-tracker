<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Filament\Resources\SubmissionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateSubmission extends CreateRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Convert submission_date to timestamp
        $submissionDate = \Carbon\Carbon::parse($data['submission_date']);
        $dayNumber = $submissionDate->timestamp;

        // Check for duplicate before attempting to create
        $existingSubmission = \App\Models\Submission::where('user_id', $data['user_id'])
            ->where('challenge_id', $data['challenge_id'])
            ->where('day_number', $dayNumber)
            ->first();

        if ($existingSubmission) {
            $formattedDate = $submissionDate->format('M d, Y');
            throw \Illuminate\Validation\ValidationException::withMessages([
                'submission_date' => "A submission already exists for this participant on {$formattedDate}. Please choose a different date or edit the existing submission.",
            ]);
        }

        // Create the submission
        $submission = \App\Models\Submission::create([
            'user_id' => $data['user_id'],
            'challenge_id' => $data['challenge_id'],
            'day_number' => $dayNumber,
            'submitted_at' => now(),
            'status' => 'pending',
        ]);

        // Save field values
        if (isset($data['fields']) && is_array($data['fields'])) {
            foreach ($data['fields'] as $ruleId => $value) {
                if (is_null($value)) {
                    continue;
                }

                $rule = \App\Models\ChallengeRule::find($ruleId);
                if (!$rule) {
                    continue;
                }

                $submissionValue = \App\Models\SubmissionValue::create([
                    'submission_id' => $submission->id,
                    'rule_id' => $ruleId,
                ]);

                // Store value based on field type
                switch ($rule->field_type) {
                    case 'number':
                        $submissionValue->value_number = is_numeric($value) ? $value : null;
                        $submissionValue->value_text = null;
                        $submissionValue->value_boolean = null;
                        break;

                    case 'checkbox':
                    case 'toggle':
                        $submissionValue->value_boolean = (bool) $value;
                        $submissionValue->value_text = null;
                        $submissionValue->value_number = null;
                        break;

                    case 'file':
                    case 'image':
                        $submissionValue->value_text = $value; // File path
                        $submissionValue->value_number = null;
                        $submissionValue->value_boolean = null;
                        break;

                    default:
                        // text, textarea, date, time, datetime, select, radio
                        $submissionValue->value_text = is_array($value) ? json_encode($value) : $value;
                        $submissionValue->value_number = null;
                        $submissionValue->value_boolean = null;
                        break;
                }

                $submissionValue->save();
            }
        }

        return $submission;
    }

    public function getTitle(): string
    {
        return 'Create Submission';
    }

    protected function getCreatedNotificationTitle(): string
    {
        return 'Submission created successfully';
    }

    protected function getCreatedNotificationMessage(): string
    {
        return 'The submission has been created and is now pending approval.';
    }
}
