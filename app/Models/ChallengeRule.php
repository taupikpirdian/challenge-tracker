<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChallengeRule extends Model
{
    protected $fillable = [
        'challenge_id',
        'label',
        'field_type',
        'is_required',
        'order_number',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    // Relationships
    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function submissionValues(): HasMany
    {
        return $this->hasMany(SubmissionValue::class, 'rule_id');
    }
}
