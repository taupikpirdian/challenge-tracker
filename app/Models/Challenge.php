<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Helpers\MinioHelper;

class Challenge extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'start_date',
        'end_date',
        'duration_days',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($challenge) {
            if (empty($challenge->slug)) {
                $challenge->slug = Str::slug($challenge->title);
            }
        });

        static::updating(function ($challenge) {
            if (empty($challenge->slug) || $challenge->isDirty('title')) {
                $challenge->slug = Str::slug($challenge->title);
            }
        });
    }

    // Accessors
    public function getCoverImageUrlAttribute(): string
    {
        if (empty($this->cover_image)) {
            return asset('images/og-default.jpg');
        }

        // If it's already a full URL, return it
        if (str_starts_with($this->cover_image, 'http')) {
            return $this->cover_image;
        }

        // Check if it's a legacy local path (starts with 'challenge-covers' or 'submissions')
        if (str_starts_with($this->cover_image, 'challenge-covers') ||
            str_starts_with($this->cover_image, 'submissions') ||
            str_starts_with($this->cover_image, 'uploads')) {
            // It's a Minio path - use proxy URL
            return MinioHelper::getProxyUrl($this->cover_image);
        }

        // Legacy path from local storage
        return asset('storage/' . $this->cover_image);
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(ChallengeRule::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChallengeParticipant::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
