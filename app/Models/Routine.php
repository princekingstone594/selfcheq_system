<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Routine extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'user_id',
        'is_completed',
        'reminder_time',
        'frequency',
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean',
        'reminder_time' => 'datetime',
    ];

    /**
     * Routines are discipline-building — every routine has an alarm.
     */
    public function getAlarmEnabledAttribute(): bool
    {
        return true;
    }

    /**
     * Get the formatted alarm time (e.g. "09:30 AM").
     */
    public function getFormattedAlarmTimeAttribute(): string
    {
        return $this->reminder_time
            ? Carbon::parse($this->reminder_time)->format('g:i A')
            : '—';
    }

    /**
     * Human-readable frequency label.
     */
    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'weekday' => '📅 Weekdays (Mon-Fri)',
            'weekend' => '📅 Weekends (Sat-Sun)',
            'daily'   => '📅 Every day',
            default   => '📅 Once',
        };
    }

    /**
     * Whether this routine should appear on the given date based on its frequency.
     */
    public function isActiveOn(string $date): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; // 0 = Sunday, 6 = Saturday

        return match ($this->frequency) {
            'weekday' => in_array($dayOfWeek, [1, 2, 3, 4, 5]),  // Mon-Fri
            'weekend' => in_array($dayOfWeek, [0, 6]),            // Sun-Sat
            'daily'   => true,
            default   => true,
        };
    }
}
