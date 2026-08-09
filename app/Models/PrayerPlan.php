<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerPlan extends Model
{
    protected $fillable = [
        'user_id',
        'prayer_point',
        'days',
        'reminder_time',
        'reminder_enabled',
        'completed_at',
    ];

    protected $casts = [
        'reminder_enabled' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->completed_at);
    }
}