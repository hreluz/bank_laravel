<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_can_login()
    {
        $this->postJson(route('api.v1.users.register'),[
            'name'      => 'Someone',
            'email'     => 'someone@gmail.com',
            'password' => '123123'
        ])->assertOk();

        $response = $this->postJson(route('api.v1.login'),[
            'email'  => 'someone@gmail.com',
            'password' => '123123'
        ]);

        $response->assertOk();
    }

    public function test_is_authenticated()
    {
        $user = User::factory()->create(['password' => '123123']);

        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.v1.auth.authenticated'));

        $this->assertEquals($response->getContent(), 'You are authenticated');
    }
}
