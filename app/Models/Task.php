<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'is_completed',
        'due_date',
        'reminder_time',
        'deadline',
        'is_important',
        'is_urgent',
    ];
}
