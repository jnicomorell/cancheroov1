<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'description',
        'points_required',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
