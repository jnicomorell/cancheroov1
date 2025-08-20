<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'amount',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
