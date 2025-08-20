<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Club, Field, Reservation};

class ReservationPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_pay_reservation(): void
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
            'payment_status' => 'pending',
        ]);

        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/reservations/'.$reservation->id.'/pay');

        $response->assertStatus(200);
        $response->assertJsonFragment(['payment_status' => 'paid']);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'payment_status' => 'paid',
        ]);
    }
}
