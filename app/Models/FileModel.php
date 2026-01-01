<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileModel extends Model
{
    protected $fillable = [
        'submission_value_id',
        'disk',
        'path',
        'mime_type',
        'size',
    ];

    // Relationships
    public function submissionValue(): BelongsTo
    {
        return $this->belongsTo(SubmissionValue::class);
    }
}
