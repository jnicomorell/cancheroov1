<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Club, Field, Reservation};

class ReservationRescheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reschedule_own_reservation(): void
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
            ->putJson('/api/reservations/'.$reservation->id, [
                'start_time' => '2025-08-22 12:00:00',
                'end_time' => '2025-08-22 14:00:00',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['start_time' => '2025-08-22T12:00:00.000000Z']);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'start_time' => '2025-08-22 12:00:00',
            'end_time' => '2025-08-22 14:00:00',
            'price' => 200,
        ]);
    }

    public function test_user_cannot_reschedule_others_reservation(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
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
            'user_id' => $other->id,
            'start_time' => '2025-08-21 10:00:00',
            'end_time' => '2025-08-21 11:00:00',
            'price' => 100,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);

        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/reservations/'.$reservation->id, [
                'start_time' => '2025-08-22 12:00:00',
                'end_time' => '2025-08-22 14:00:00',
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'start_time' => '2025-08-21 10:00:00',
        ]);
    }
}

