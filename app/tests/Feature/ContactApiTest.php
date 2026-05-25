<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_contacts_list(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Contact::factory()->count(3)->create();

        $response = $this->getJson('/api/contacts');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'company_id',
                        'company',
                        'first_name',
                        'last_name',
                        'email',
                        'phone',
                        'position',
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

    public function test_it_creates_contact(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $company = Company::factory()->create();

        $response = $this->postJson('/api/contacts', [
            'company_id' => $company->id,
            'first_name' => 'Anna',
            'last_name' => 'Ivanova',
            'email' => 'anna@example.com',
            'phone' => '+7 999 777-88-99',
            'position' => 'Marketing Manager',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.first_name', 'Anna');

        $this->assertDatabaseHas('contacts', [
            'company_id' => $company->id,
            'email' => 'anna@example.com',
        ]);
    }

    public function test_it_validates_required_contact_fields(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/contacts', [
            'email' => 'invalid@example.com',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['company_id', 'first_name']);
    }

    public function test_it_returns_contact_with_company(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $company = Company::factory()->create([
            'name' => 'Acme Inc',
        ]);

        $contact = Contact::factory()->create([
            'company_id' => $company->id,
            'first_name' => 'Ivan',
        ]);

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Ivan')
            ->assertJsonPath('data.company.id', $company->id)
            ->assertJsonPath('data.company.name', 'Acme Inc');
    }
}