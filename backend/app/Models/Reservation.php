<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'field_id',
        'user_id',
        'start_time',
        'end_time',
        'price',
        'status',
        'payment_status',
        'weather_alert',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'payment_status' => 'string',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reservation_participants')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public static function createPeriodic(array $data, int $count): array
    {
        $reservations = [];
        for ($i = 0; $i < $count; $i++) {
            $start = Carbon::parse($data['start_time'])->copy()->addWeek($i);
            $end = Carbon::parse($data['end_time'])->copy()->addWeek($i);
            $reservations[] = self::create(array_merge($data, [
                'start_time' => $start,
                'end_time' => $end,
            ]));
        }
        return $reservations;
    }
}
