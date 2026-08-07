<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'mood',
        'gratitude',
        'reflection',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'mood' => 'integer',
    ];

    /**
     * Get a short snip (first few words) of the journal content.
     * Used for dashboard preview.
     */
    public function getSnipAttribute(): string
    {
        $content = trim($this->content ?? '');

        if ($content === '') {
            return 'No entry text yet.';
        }

        // Take the first 12 words
        $words = explode(' ', $content);
        $snip = implode(' ', array_slice($words, 0, 12));

        if (count($words) > 12) {
            $snip .= '…';
        }

        return $snip;
    }
}
