<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_clients(): void
    {
        $response = $this->getJson('/api/clients');

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_clients(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Client::factory()->count(3)->create();

        $response = $this->getJson('/api/clients');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'company',
                        'comment',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);
    }

    public function test_authenticated_user_can_create_client(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $payload = [
            'name' => 'Ivan Petrov',
            'email' => 'ivan@example.com',
            'phone' => '+7 999 111-22-33',
            'company' => 'Acme Inc',
            'comment' => 'Created from test',
        ];

        $response = $this->postJson('/api/clients', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Ivan Petrov')
            ->assertJsonPath('data.email', 'ivan@example.com')
            ->assertJsonPath('data.phone', '+7 999 111-22-33')
            ->assertJsonPath('data.company', 'Acme Inc')
            ->assertJsonPath('data.comment', 'Created from test');

        $this->assertDatabaseHas('clients', [
            'name' => 'Ivan Petrov',
            'email' => 'ivan@example.com',
            'phone' => '+7 999 111-22-33',
            'company' => 'Acme Inc',
            'comment' => 'Created from test',
        ]);
    }

    public function test_client_name_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/clients', [
            'email' => 'ivan@example.com',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_client_email_must_be_valid(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/clients', [
            'name' => 'Ivan Petrov',
            'email' => 'not-an-email',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_show_client(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $client = Client::factory()->create([
            'name' => 'Maria Smirnova',
            'email' => 'maria@example.com',
        ]);

        $response = $this->getJson("/api/clients/{$client->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $client->id)
            ->assertJsonPath('data.name', 'Maria Smirnova')
            ->assertJsonPath('data.email', 'maria@example.com');
    }

    public function test_authenticated_user_can_update_client(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $client = Client::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '+7 999 000-00-00',
            'company' => 'Old Company',
            'comment' => 'Old comment',
        ]);

        $response = $this->patchJson("/api/clients/{$client->id}", [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '+7 999 111-11-11',
            'company' => 'New Company',
            'comment' => 'New comment',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $client->id)
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.email', 'new@example.com')
            ->assertJsonPath('data.phone', '+7 999 111-11-11')
            ->assertJsonPath('data.company', 'New Company')
            ->assertJsonPath('data.comment', 'New comment');

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '+7 999 111-11-11',
            'company' => 'New Company',
            'comment' => 'New comment',
        ]);
    }

    public function test_authenticated_user_can_delete_client(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $client = Client::factory()->create();

        $response = $this->deleteJson("/api/clients/{$client->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    public function test_authenticated_user_can_search_clients(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Client::factory()->create([
            'name' => 'Ivan Petrov',
            'email' => 'ivan@example.com',
            'company' => 'Acme Inc',
        ]);

        Client::factory()->create([
            'name' => 'Maria Smirnova',
            'email' => 'maria@example.com',
            'company' => 'Beta Group',
        ]);

        $response = $this->getJson('/api/clients?search=acme');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Ivan Petrov')
            ->assertJsonPath('data.0.company', 'Acme Inc');
    }
}