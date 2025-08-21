<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'recurrence_count' => 'integer',
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
            ->using(ReservationParticipant::class)
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function waitlist(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'reservation_waitlist')->withTimestamps();
    }

    public static function createWithRecurrence(array $data): array
    {
        $count = $data['recurrence_count'] ?? 1;
        $interval = $data['recurrence_interval'] ?? null;
        $reservations = [];
        $reservations[] = self::create($data);

        if ($interval && $count > 1) {
            $currentStart = Carbon::parse($data['start_time']);
            $currentEnd = Carbon::parse($data['end_time']);
            for ($i = 1; $i < $count; $i++) {
                switch ($interval) {
                    case 'daily':
                        $currentStart->addDay();
                        $currentEnd->addDay();
                        break;
                    case 'weekly':
                        $currentStart->addWeek();
                        $currentEnd->addWeek();
                        break;
                    default:
                        break 2;
                }

                $reservations[] = self::create(array_merge($data, [
                    'start_time' => $currentStart->copy(),
                    'end_time' => $currentEnd->copy(),
                    'recurrence_interval' => null,
                    'recurrence_count' => 1,
                ]));
            }
        }

        return $reservations;
    }
}
