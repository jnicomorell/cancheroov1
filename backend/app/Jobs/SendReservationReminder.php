<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendReservationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Reservation $reservation)
    {
    }

    public function handle(): void
    {
        $reservation = $this->reservation->fresh(['user', 'field.club']);
        if (! $reservation || ! $reservation->user->fcm_token) {
            return;
        }

        $club = $reservation->field->club;
        $message = 'Recordatorio: tienes un partido en ' . $reservation->field->name .
            ' a las ' . $reservation->start_time->format('H:i');

        if ($club->latitude && $club->longitude) {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $club->latitude,
                'longitude' => $club->longitude,
                'hourly' => 'precipitation',
                'start' => $reservation->start_time->subHour()->toIso8601String(),
                'end' => $reservation->end_time->toIso8601String(),
            ]);
            if ($response->ok()) {
                $precip = $response->json('hourly.precipitation.0');
                if ($precip > 0) {
                    $message .= '. Se esperan precipitaciones';
                }
            }
        }

        Http::withToken(config('services.fcm.key'))
            ->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $reservation->user->fcm_token,
                'notification' => [
                    'title' => 'Canchero',
                    'body' => $message,
                ],
                'data' => [
                    'reservation_id' => $reservation->id,
                ],
            ]);
    }
}
