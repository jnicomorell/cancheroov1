<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'day',
        'start_time',
        'end_time',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
