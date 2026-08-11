<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'is_completed',
        'due_date',
        'reminder_time',
        'deadline',
        'is_important',
        'is_urgent',
        'alarm_enabled',
        'alarm_time',
        'type',
        'reference_id',
        'frequency',
    ];

    protected $casts = [
        'due_date' => 'date',
        'deadline' => 'date',
        'is_completed' => 'boolean',
        'is_important' => 'boolean',
        'is_urgent' => 'boolean',
        'alarm_enabled' => 'boolean',
    ];

    /**
     * Get the formatted alarm time (e.g. "09:30 AM").
     */
    public function getFormattedAlarmTimeAttribute(): string
    {
        $time = $this->alarm_time ?? $this->reminder_time;

        return $time ? Carbon::parse($time)->format('g:i A') : '—';
    }

    public function financial()
    {
        return $this->belongsTo(Financial::class, 'reference_id');
    }
}
