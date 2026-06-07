<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Laravel\Sanctum\Sanctum;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_companies_list(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Company::factory()->count(3)->create();

        $response = $this->getJson('/api/companies');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'owner_id',
                        'owner',
                        'website',
                        'phone',
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

    public function test_it_creates_company(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/companies', [
            'name' => 'Test Company',
            'website' => 'https://test-company.test',
            'phone' => '+7 999 555-66-77',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.owner_id', $user->id)
            ->assertJsonPath('data.name', 'Test Company');

        $this->assertDatabaseHas('companies', [
            'owner_id' => $user->id,
            'name' => 'Test Company',
        ]);
    }

    public function test_it_validates_required_company_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/companies', [
            'website' => 'https://invalid.test',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_it_returns_company_with_contacts_and_deals(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);
    
        $company = Company::factory()->create([
            'owner_id' => $user->id,
        ]);

        $company->contacts()->create([
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'email' => 'ivan@example.com',
            'phone' => '+7 999 222-33-44',
            'position' => 'CEO',
        ]);

        $company->deals()->create([
            'title' => 'Test Deal',
            'amount' => 100000,
            'status' => 'new',
            'expected_close_date' => '2026-06-30',
        ]);

        $response = $this->getJson("/api/companies/{$company->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $company->id)
            ->assertJsonPath('data.contacts.0.first_name', 'Ivan')
            ->assertJsonPath('data.deals.0.title', 'Test Deal');
    }

    public function test_user_only_sees_own_companies(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        Company::factory()->create([
            'owner_id' => $user->id,
            'name' => 'My Company',
        ]);

        Company::factory()->create([
            'owner_id' => $otherUser->id,
            'name' => 'Other Company',
        ]);

        $response = $this->getJson('/api/companies');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'My Company')
            ->assertJsonMissing([
                'name' => 'Other Company',
            ]);
    }

    public function test_user_cannot_show_other_users_company(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $otherCompany = Company::factory()->create([
            'owner_id' => $otherUser->id,
        ]);

        $response = $this->getJson("/api/companies/{$otherCompany->id}");

        $response->assertForbidden();
    }

    public function test_user_cannot_update_other_users_company(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $otherCompany = Company::factory()->create([
            'owner_id' => $otherUser->id,
            'name' => 'Other Company',
        ]);

        $response = $this->patchJson("/api/companies/{$otherCompany->id}", [
            'name' => 'Hacked Company',
            'website' => 'https://hacked.example.com',
            'phone' => '+7 999 000-00-00',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('companies', [
            'id' => $otherCompany->id,
            'name' => 'Other Company',
        ]);
    }
    
    public function test_user_cannot_delete_other_users_company(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $otherCompany = Company::factory()->create([
            'owner_id' => $otherUser->id,
        ]);

        $response = $this->deleteJson("/api/companies/{$otherCompany->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('companies', [
            'id' => $otherCompany->id,
        ]);
    }
}