<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class BaseApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function authAs(?User $user = null)
    {
        if(!$user) {
            $user = User::factory()->create();
        }

        Sanctum::actingAs($user);
    }
}
