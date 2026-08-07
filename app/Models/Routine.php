<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'user_id',
        'is_completed'
    ];
}
