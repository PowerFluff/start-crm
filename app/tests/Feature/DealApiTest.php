<?php

namespace Tests\Feature;

use App\Enums\DealStatus;
use App\Models\Company;
use App\Models\Deal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;


class DealApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_deals_list(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Deal::factory()->count(3)->create();

        $response = $this->getJson('/api/deals');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'company_id',
                        'company',
                        'title',
                        'amount',
                        'status',
                        'expected_close_date',
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

    public function test_it_creates_deal(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $company = Company::factory()->create();

        $response = $this->postJson('/api/deals', [
            'company_id' => $company->id,
            'title' => 'Important Deal',
            'amount' => 250000,
            'status' => DealStatus::InProgress->value,
            'expected_close_date' => '2026-07-15',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Important Deal')
            ->assertJsonPath('data.status', DealStatus::InProgress->value)
            ->assertJsonPath('data.company.id', $company->id);

        $this->assertDatabaseHas('deals', [
            'company_id' => $company->id,
            'title' => 'Important Deal',
            'status' => DealStatus::InProgress->value,
        ]);
    }

    public function test_it_validates_deal_status(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $company = Company::factory()->create();

        $response = $this->postJson('/api/deals', [
            'company_id' => $company->id,
            'title' => 'Invalid Deal',
            'amount' => 1000,
            'status' => 'banana',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_it_filters_deals_by_status(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Deal::factory()->create([
            'status' => DealStatus::New->value,
        ]);

        Deal::factory()->create([
            'status' => DealStatus::Won->value,
        ]);

        $response = $this->getJson('/api/deals?status=' . DealStatus::Won->value);

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.status', DealStatus::Won->value);
    }
}