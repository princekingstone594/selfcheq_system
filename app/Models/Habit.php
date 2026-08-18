<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'emoji',
        'description',
        'target_value',
        'unit',
        'frequency',
        'is_active',
        'reminder_time',
        'xp_reward',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reminder_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(HabitCompletion::class);
    }

    /**
     * Get today's completion for this habit.
     */
    public function todayCompletion(?string $date = null): ?HabitCompletion
    {
        $date = $date ?? Carbon::today()->toDateString();
        return $this->completions()->whereDate('date', $date)->first();
    }

    /**
     * Check if the habit is completed today.
     */
    public function isCompletedToday(?string $date = null): bool
    {
        $date = $date ?? Carbon::today()->toDateString();
        $completion = $this->completions()->whereDate('date', $date)->first();
        return $completion && $completion->value >= ($this->target_value ?? 1);
    }

    /**
     * Get the current streak for this habit.
     */
    public function currentStreak(): int
    {
        $streak = 0;
        $date = Carbon::today();

        for ($i = 0; $i < 100; $i++) {
            $completion = $this->completions()
                ->whereDate('date', $date->copy()->subDays($i)->toDateString())
                ->first();

            if ($completion && $completion->value >= ($this->target_value ?? 1)) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get completion rate over the last N days.
     */
    public function completionRate(int $days = 7): float
    {
        $completed = 0;

        for ($i = 0; $i < $days; $i++) {
            if ($this->isCompletedToday(Carbon::today()->copy()->subDays($i)->toDateString())) {
                $completed++;
            }
        }

        return $days > 0 ? ($completed / $days) * 100 : 0;
    }

    /**
     * Whether this habit should appear today based on its frequency.
     */
    public function isActiveOn(string $date): bool
    {
        if ($this->frequency === 'daily') {
            return true;
        }

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        return match ($this->frequency) {
            'weekday' => in_array($dayOfWeek, [1, 2, 3, 4, 5]),
            'weekend' => in_array($dayOfWeek, [0, 6]),
            'weekly'  => true, // appears every day but you track it weekly
            'monthly' => true,
            default   => true,
        };
    }
}
