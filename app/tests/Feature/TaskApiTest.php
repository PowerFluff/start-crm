<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Deal;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_tasks_list(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'deal_id',
                        'deal',
                        'title',
                        'description',
                        'status',
                        'due_at',
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

    public function test_it_creates_task(): void
    {
        $deal = Deal::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'deal_id' => $deal->id,
            'title' => 'Prepare proposal',
            'description' => 'Send commercial offer',
            'status' => TaskStatus::Todo->value,
            'due_at' => '2026-06-01 10:00:00',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Prepare proposal')
            ->assertJsonPath('data.status', TaskStatus::Todo->value)
            ->assertJsonPath('data.deal.id', $deal->id);

        $this->assertDatabaseHas('tasks', [
            'deal_id' => $deal->id,
            'title' => 'Prepare proposal',
            'status' => TaskStatus::Todo->value,
        ]);
    }

    public function test_it_validates_task_status(): void
    {
        $deal = Deal::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'deal_id' => $deal->id,
            'title' => 'Invalid Task',
            'status' => 'banana',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_it_filters_tasks_by_status(): void
    {
        Task::factory()->create([
            'status' => TaskStatus::Todo->value,
        ]);

        Task::factory()->create([
            'status' => TaskStatus::Done->value,
        ]);

        $response = $this->getJson('/api/tasks?status=' . TaskStatus::Done->value);

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.status', TaskStatus::Done->value);
    }

    public function test_it_filters_tasks_by_deal(): void
    {
        $targetDeal = Deal::factory()->create();
        $otherDeal = Deal::factory()->create();

        Task::factory()->create([
            'deal_id' => $targetDeal->id,
        ]);

        Task::factory()->create([
            'deal_id' => $otherDeal->id,
        ]);

        $response = $this->getJson('/api/tasks?deal_id=' . $targetDeal->id);

        $response
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.deal_id', $targetDeal->id);
    }
}