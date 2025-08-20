<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\{User, Club, Field};

class FieldManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createField(User $user): Field
    {
        $club = Club::create([
            'user_id' => $user->id,
            'name' => 'Club',
            'address' => 'Addr',
            'city' => 'City',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        return Field::create([
            'club_id' => $club->id,
            'name' => 'Field',
            'sport' => 'futbol',
            'price_per_hour' => 100,
        ]);
    }

    public function test_admin_can_update_field(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $field = $this->createField($admin);

        $token = $admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/fields/'.$field->id, [
                'name' => 'Updated Field',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('fields', [
            'id' => $field->id,
            'name' => 'Updated Field',
        ]);
    }

    public function test_client_cannot_update_field(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $field = $this->createField($admin);
        $client = User::factory()->create();

        $token = $client->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/fields/'.$field->id, [
                'name' => 'Updated Field',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_field(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $field = $this->createField($admin);

        $token = $admin->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/fields/'.$field->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('fields', [
            'id' => $field->id,
        ]);
    }

    public function test_client_cannot_delete_field(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $field = $this->createField($admin);
        $client = User::factory()->create();

        $token = $client->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/fields/'.$field->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('fields', [
            'id' => $field->id,
        ]);
    }
}
