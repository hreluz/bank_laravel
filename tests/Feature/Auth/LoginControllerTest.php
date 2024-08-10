<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BaseApiTestCase;

class LoginControllerTest extends BaseApiTestCase
{
    use RefreshDatabase, UserStructureTrait;

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

        $this->postJson(route('api.v1.login'),[
            'email'  => 'someone@gmail.com',
            'password' => '123123'
        ]) ->assertOk()
            ->assertJsonStructure([
            'data' => [
                'access_token',
                'expires_in_seconds',
                'user' => $this->userStructure() ,
            ]
        ]);


    }

    /**
     * @return void
     */
    public function test_is_authenticated()
    {
        $user = User::factory()->create(['password' => '123123']);

        $this->authAs($user);

        $response = $this->getJson(route('api.v1.auth.authenticated'));

        $this->assertEquals($response->getContent(), 'You are authenticated');
    }
}
