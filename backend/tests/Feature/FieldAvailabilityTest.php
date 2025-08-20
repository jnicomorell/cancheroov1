<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Club, Field, Reservation};

class FieldAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_fields_by_availability(): void
    {
        $user = User::factory()->create();

        $club = Club::create([
            'user_id' => $user->id,
            'name' => 'Club A',
            'address' => 'Addr',
            'city' => 'City',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        $field1 = Field::create([
            'club_id' => $club->id,
            'name' => 'Field 1',
            'sport' => 'futbol',
            'price_per_hour' => 100,
        ]);

        $field2 = Field::create([
            'club_id' => $club->id,
            'name' => 'Field 2',
            'sport' => 'futbol',
            'price_per_hour' => 100,
        ]);

        Reservation::create([
            'field_id' => $field1->id,
            'user_id' => $user->id,
            'start_time' => '2025-08-21 10:00:00',
            'end_time' => '2025-08-21 11:00:00',
            'price' => 100,
            'status' => 'confirmed',
            'paid' => false,
        ]);

        $response = $this->getJson('/api/fields?start_time=2025-08-21%2010:00:00&end_time=2025-08-21%2011:00:00');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $field2->id]);
        $this->assertFalse(collect($response->json('data'))->pluck('id')->contains($field1->id));
    }
}
