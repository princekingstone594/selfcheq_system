<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitnessEntry extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'details',
        'day_of_week',
        'linked_task_id',
        'linked_routine_id',
        'is_done',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public const TYPES = ['nutrition', 'workout', 'gym'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function linkedTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'linked_task_id');
    }

    public function linkedRoutine(): BelongsTo
    {
        return $this->belongsTo(Routine::class, 'linked_routine_id');
    }

    public static function dayName(?int $day): string
    {
        return $day ? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$day - 1] : 'Any day';
    }
}
