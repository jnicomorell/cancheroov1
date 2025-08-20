<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Club, Field, Reservation};

class ReservationParticipantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_attach_participants_with_costs(): void
    {
        $owner = User::factory()->create();
        $participant = User::factory()->create();
        $club = Club::create([
            'user_id' => $owner->id,
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
            'user_id' => $owner->id,
            'start_time' => '2025-08-21 10:00:00',
            'end_time' => '2025-08-21 11:00:00',
            'price' => 100,
            'status' => 'confirmed',
            'paid' => false,
        ]);

        $reservation->participants()->attach($participant->id, ['amount' => 50]);

        $this->assertDatabaseHas('reservation_participants', [
            'reservation_id' => $reservation->id,
            'user_id' => $participant->id,
            'amount' => 50,
        ]);
    }
}

