<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Services\WeatherService;

class SendReservationReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Reservation $reservation)
    {
    }

    public function handle(WeatherService $weatherService): void
    {
        $reservation = $this->reservation->fresh(['user', 'field.club']);
        if (! $reservation || ! $reservation->user->fcm_token) {
            return;
        }

        $club = $reservation->field->club;
        $message = 'Recordatorio: tienes un partido en ' . $reservation->field->name .
            ' a las ' . $reservation->start_time->format('H:i');

        if ($club->latitude && $club->longitude) {
            $prev = $reservation->weather_alert;
            $alert = $weatherService->getAlert(
                $club->latitude,
                $club->longitude,
                $reservation->start_time,
                $reservation->end_time
            );

            $changed = $alert !== $prev;
            if ($changed) {
                $reservation->weather_alert = $alert;
                $reservation->save();
            }

            if ($alert) {
                $message .= $changed ? '. Pronóstico actualizado: ' . $alert : '. ' . $alert;
            } elseif ($changed) {
                $message .= '. Pronóstico actualizado: sin alertas';
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
