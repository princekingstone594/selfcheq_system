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
}
