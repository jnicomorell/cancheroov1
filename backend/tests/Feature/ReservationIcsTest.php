<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Club, Field, Reservation};

class ReservationIcsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_download_reservation_ics(): void
    {
        $user = User::factory()->create();
        $club = Club::create([
            'user_id' => $user->id,
            'name' => 'Club',
            'address' => 'Addr',
            'city' => 'City',
            'latitude' => 0,
            'longitude' => 0,
        ]);
        $field = Field::create([
            'club_id' => $club->id,
            'name' => 'Field',
            'sport' => 'futbol',
            'price_per_hour' => 100,
        ]);
        $reservation = Reservation::create([
            'field_id' => $field->id,
            'user_id' => $user->id,
            'start_time' => '2025-08-21 10:00:00',
            'end_time' => '2025-08-21 11:00:00',
            'price' => 100,
            'status' => 'confirmed',
            'paid' => false,
        ]);

        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get('/api/reservations/'.$reservation->id.'/ics');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $response->assertSee('BEGIN:VEVENT');
    }
}
