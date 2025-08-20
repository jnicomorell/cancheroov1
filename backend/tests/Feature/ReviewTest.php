<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Club, Field, Review};

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_review_and_field_shows_average_rating(): void
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

        $field = Field::create([
            'club_id' => $club->id,
            'name' => 'Field 1',
            'sport' => 'futbol',
            'price_per_hour' => 100,
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/reviews', [
            'field_id' => $field->id,
            'rating' => 4,
            'comment' => 'Good',
        ]);

        $response->assertStatus(201)->assertJsonFragment(['rating' => 4, 'comment' => 'Good']);

        $otherUser = User::factory()->create();
        Review::create([
            'field_id' => $field->id,
            'user_id' => $otherUser->id,
            'rating' => 2,
            'comment' => 'Bad',
        ]);

        $fieldResponse = $this->getJson("/api/fields/{$field->id}");
        $fieldResponse->assertStatus(200);
        $this->assertEquals(3, $fieldResponse->json('average_rating'));

        $listResponse = $this->getJson('/api/fields');
        $listResponse->assertStatus(200);
        $this->assertEquals(3, $listResponse->json('data.0.average_rating'));
    }

    public function test_user_can_update_and_delete_review(): void
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

        $field = Field::create([
            'club_id' => $club->id,
            'name' => 'Field 1',
            'sport' => 'futbol',
            'price_per_hour' => 100,
        ]);

        $review = Review::create([
            'field_id' => $field->id,
            'user_id' => $user->id,
            'rating' => 3,
            'comment' => 'Ok',
        ]);

        $this->actingAs($user);

        $update = $this->putJson("/api/reviews/{$review->id}", [
            'rating' => 5,
            'comment' => 'Great',
        ]);
        $update->assertStatus(200)->assertJsonFragment(['rating' => 5, 'comment' => 'Great']);

        $delete = $this->deleteJson("/api/reviews/{$review->id}");
        $delete->assertStatus(200);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
