<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    protected $fillable = [
        'club_id',
        'name',
        'sport',
        'surface',
        'is_indoor',
        'latitude',
        'longitude',
        'price_per_hour',
        'features',
    ];

    protected $casts = [
        'is_indoor' => 'boolean',
        'features' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
