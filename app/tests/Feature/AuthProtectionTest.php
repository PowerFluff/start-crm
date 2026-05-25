<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_crm_routes_require_authentication(): void
    {
        $this->getJson('/api/companies')->assertUnauthorized();
        $this->getJson('/api/contacts')->assertUnauthorized();
        $this->getJson('/api/deals')->assertUnauthorized();
        $this->getJson('/api/tasks')->assertUnauthorized();
    }
}