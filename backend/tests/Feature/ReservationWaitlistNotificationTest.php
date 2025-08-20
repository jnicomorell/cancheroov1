<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Models\{User, Club, Field, Reservation};
use App\Jobs\NotifyWaitlist;

class ReservationWaitlistNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifies_waitlist_on_cancellation(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $waitlisted = User::factory()->create(['fcm_token' => 'token']);
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
        $reservation->waitlist()->attach($waitlisted->id);

        $token = $user->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/reservations/'.$reservation->id);

        $response->assertStatus(200);
        Queue::assertPushed(NotifyWaitlist::class, function ($job) use ($reservation) {
            return $job->reservation->is($reservation);
        });
    }
}
