<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MorningDevotion extends Model
{
    protected $fillable = [
        'user_id',
        'wake_up_time',
        'alarm_enabled',
        'declaration',
        'is_active',
    ];

    protected $casts = [
        'alarm_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}