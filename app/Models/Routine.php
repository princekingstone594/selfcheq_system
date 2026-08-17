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
        'reference_id',
        'reference_type',
        'is_permanent',
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean',
        'reminder_time' => 'datetime',
        'is_permanent' => 'boolean',
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
            'weekday'  => '📅 Weekdays (Mon-Fri)',
            'weekend'  => '📅 Weekends (Sat-Sun)',
            'daily'    => '📅 Every day',
            'weekly'   => '📅 Every week',
            'monthly'  => '📅 Every month',
            'quarterly'=> '📅 Every 3 months',
            'annually' => '📅 Every year',
            'once'     => '📅 One-time',
            default    => '📅 Once',
        };
    }

    /**
     * Whether this routine should appear on the given date based on its frequency.
     *
     * Daily-templated routines (weekday/weekend/daily) recur every matching day.
     * Calendar-based recurring routines (weekly/monthly/quarterly/annually) are
     * materialized by the RoutineController's carry-forward logic, so they are
     * NOT copied here day-over-day.
     */
    public function isActiveOn(string $date): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; // 0 = Sunday, 6 = Saturday

        return match ($this->frequency) {
            'weekday'        => in_array($dayOfWeek, [1, 2, 3, 4, 5]),  // Mon-Fri
            'weekend'        => in_array($dayOfWeek, [0, 6]),            // Sun-Sat
            'daily'          => true,
            'weekly'         => false, // handled by materialization / calendar projection
            'monthly'        => false,
            'quarterly'      => false,
            'annually'       => false,
            'once'           => Carbon::parse($this->date)->toDateString() === $date,
            default          => false,
        };
    }

    /**
     * Days interval for this frequency (for carry-forward recurrence).
     */
    public function getRecurrenceInterval(): ?int
    {
        return match ($this->frequency) {
            'weekly'    => 7,
            'monthly'   => 30,
            'quarterly' => 90,
            'annually'  => 365,
            default     => null,
        };
    }

    public function financial()
    {
        return $this->belongsTo(Financial::class, 'reference_id');
    }
}
