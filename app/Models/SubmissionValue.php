<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\MinioHelper;

class SubmissionValue extends Model
{
    protected $fillable = [
        'submission_id',
        'rule_id',
        'value_text',
        'value_number',
        'value_boolean',
    ];

    protected $casts = [
        'value_boolean' => 'boolean',
        'value_number' => 'decimal:2',
    ];

    // Accessors
    public function getFileUrlAttribute(): ?string
    {
        // Only return URL if this is a file/image field and value_text is not empty
        if (empty($this->value_text)) {
            return null;
        }

        $rule = $this->rule;
        if (!$rule || !in_array($rule->field_type, ['file', 'image'])) {
            return null;
        }

        // If it's already a full URL, return it
        if (str_starts_with($this->value_text, 'http')) {
            return $this->value_text;
        }

        // Check if it's a Minio path
        if (str_starts_with($this->value_text, 'submissions') ||
            str_starts_with($this->value_text, 'uploads')) {
            return MinioHelper::getProxyUrl($this->value_text);
        }

        // Legacy path from local storage
        return asset('storage/' . $this->value_text);
    }

    public function getIsFileAttribute(): bool
    {
        $rule = $this->rule;
        return $rule && in_array($rule->field_type, ['file', 'image']);
    }

    // Relationships
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(ChallengeRule::class, 'rule_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(FileModel::class, 'submission_value_id');
    }
}
