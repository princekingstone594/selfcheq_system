<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Financial extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'amount',
        'frequency',
        'due_date',
        'is_completed',
        'reminder_days',
        'is_recurring',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'is_completed' => 'boolean',
            'is_recurring' => 'boolean',
            'amount' => 'decimal:2',
            'reminder_days' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'reference_id');
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class, 'reference_id');
    }
}
