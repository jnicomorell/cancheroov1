<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Club, Field, Reservation};

class ReservationPeriodicTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_periodic_reservations(): void
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

        $data = [
            'field_id' => $field->id,
            'user_id' => $user->id,
            'start_time' => '2025-08-21 10:00:00',
            'end_time' => '2025-08-21 11:00:00',
            'price' => 100,
            'status' => 'confirmed',
            'payment_status' => 'pending',
            'recurrence_interval' => 'weekly',
            'recurrence_count' => 3,
        ];

        Reservation::createWithRecurrence($data);

        $this->assertDatabaseCount('reservations', 3);
        $this->assertDatabaseHas('reservations', ['start_time' => '2025-08-21 10:00:00']);
        $this->assertDatabaseHas('reservations', ['start_time' => '2025-08-28 10:00:00']);
        $this->assertDatabaseHas('reservations', ['start_time' => '2025-09-04 10:00:00']);
    }
}

