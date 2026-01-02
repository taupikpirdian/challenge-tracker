<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $fillable = [
        'user_id',
        'challenge_id',
        'day_number',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Accessor to get human-readable date from day_number timestamp
    public function getSubmissionDateAttribute(): string
    {
        return \Carbon\Carbon::createFromTimestamp($this->day_number)->format('Y-m-d');
    }

    // Accessor to get formatted date for display
    public function getFormattedDateAttribute(): string
    {
        return \Carbon\Carbon::createFromTimestamp($this->day_number)->format('M d, Y');
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(SubmissionValue::class);
    }
}
