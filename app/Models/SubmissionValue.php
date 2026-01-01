<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
