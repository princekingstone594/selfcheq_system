<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FastingPlan extends Model
{
    protected $fillable = [
        'user_id',
        'purpose',
        'days',
        'reminder_time',
        'reminder_enabled',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'reminder_enabled' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return !is_null($this->started_at) && is_null($this->completed_at);
    }

    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->completed_at);
    }
}