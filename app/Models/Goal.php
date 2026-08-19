<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'target_score',
        'current_score',
        'start_date',
        'end_date',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate progress percentage toward the target score.
     */
    public function getProgressAttribute(): int
    {
        if ($this->target_score <= 0) return 0;
        return min(100, round(($this->current_score / $this->target_score) * 100));
    }

    /**
     * Get days remaining until the goal end date.
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, Carbon::today()->diffInDays($this->end_date, false));
    }

    /**
     * Check if the goal is on track based on elapsed time vs progress.
     */
    public function getIsOnTrackAttribute(): bool
    {
        $totalDays = Carbon::parse($this->start_date)->diffInDays($this->end_date);
        if ($totalDays <= 0) return false;

        $elapsedDays = Carbon::parse($this->start_date)->diffInDays(Carbon::today());
        $expectedProgress = min(100, ($elapsedDays / $totalDays) * 100);

        return $this->progress >= $expectedProgress;
    }
}