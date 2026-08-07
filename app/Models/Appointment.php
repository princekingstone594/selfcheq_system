<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'time',
        'date',
        'is_completed',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the formatted time (e.g. "09:30 AM").
     */
    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->time)->format('g:i A');
    }

    /**
     * Get the formatted date (e.g. "Mon, 07 Aug 2026").
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date ? Carbon::parse($this->date)->format('D, d M Y') : '';
    }
}
