<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class NotifyWaitlist implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Reservation $reservation)
    {
    }

    public function handle(): void
    {
        $reservation = $this->reservation->load(['waitlist', 'field']);
        if (! $reservation) {
            return;
        }

        foreach ($reservation->waitlist as $user) {
            if (! $user->fcm_token) {
                continue;
            }

            Http::withToken(config('services.fcm.key'))
                ->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $user->fcm_token,
                    'notification' => [
                        'title' => 'Canchero',
                        'body' => 'Turno disponible en ' . $reservation->field->name,
                    ],
                    'data' => [
                        'reservation_id' => $reservation->id,
                    ],
                ]);
        }
    }
}
