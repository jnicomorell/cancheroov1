<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getAlert(float $latitude, float $longitude, $start, $end): ?string
    {
        $start = $start instanceof Carbon ? $start : Carbon::parse($start);
        $end = $end instanceof Carbon ? $end : Carbon::parse($end);

        if (app()->environment('testing')) {
            return null;
        }

        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hourly' => 'precipitation',
            'start' => $start->subHour()->toIso8601String(),
            'end' => $end->toIso8601String(),
        ]);

        if (! $response->ok()) {
            return null;
        }

        $precip = $response->json('hourly.precipitation.0');
        return $precip > 0 ? 'Se esperan precipitaciones' : null;
    }
}

