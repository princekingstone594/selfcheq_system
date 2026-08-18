<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    protected $fillable = [
       'user_id',
       'date',
       'score',
       'tasks_completed',
       'tasks_total',
       'focus_minutes',
       'mood',
       'journaled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
