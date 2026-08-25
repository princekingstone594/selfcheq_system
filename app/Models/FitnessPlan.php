<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitnessPlan extends Model
{
    protected $fillable = [
        'user_id',
        'goal',
        'level',
        'week_start',
        'plan',
        'completed_days',
        'is_active',
    ];

    protected $casts = [
        'plan' => 'array',
        'completed_days' => 'array',
        'week_start' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
