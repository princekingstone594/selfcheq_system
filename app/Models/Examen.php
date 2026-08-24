<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Examen extends Model
{
    protected $fillable = [
        'user_id',
        'mood_rating',
        'high_point',
        'reflection',
        'gratitude',
        'released',
        'date',
    ];

    protected $casts = [
        'mood_rating' => 'integer',
        'released' => 'boolean',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Short human label for the mood rating.
     */
    public function getMoodLabelAttribute(): string
    {
        return match ($this->mood_rating ?? 3) {
            1 => '😞 Meh',
            2 => '😐 Okay',
            3 => '🙂 Steady',
            4 => '🙂 Good',
            5 => '🔥 Great',
            default => '—',
        };
    }

    /**
     * Emoji for the mood rating (for history lists).
     */
    public function getMoodEmojiAttribute(): string
    {
        return match ($this->mood_rating ?? 3) {
            1 => '😞',
            2 => '😐',
            3 => '🙂',
            4 => '😄',
            5 => '🔥',
            default => '—',
        };
    }
}