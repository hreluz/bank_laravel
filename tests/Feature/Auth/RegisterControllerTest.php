<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Tests\BaseApiTestCase;
use Tests\Traits\UserStructureTrait;

class RegisterControllerTest extends BaseApiTestCase
{
    use RefreshDatabase, UserStructureTrait;

    /**
     * @return void
     */
    public function test_it_allows_user_to_register()
    {
        $name = 'James';
        $email = 'jbond@gmail.com';

        $user = User::factory()->make([
            'name'      => $name,
            'email'     => $email,
            'password'  => '12313'
        ]);

        $response = $this->postJson(route('api.v1.users.register'), array_merge($user->toArray(), [
            'password' => '123123'
        ]));

        $response->assertJsonStructure([
            'data' => [
                'user' => $this->userStructure()
            ]
        ]);

        $response->assertOk()
            ->assertJson([
                'success'    => true,
                'data'  => [
                    'user' => [
                        'name'              => $name,
                        'email'             => $email,
                    ]
                ],
                'message'   => JsonResponse::HTTP_OK,
            ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'name'      => $name,
            'email' => $email,
        ]);
    }

    /**
     * @return void
     */
    public function test_it_validates_empty_fields_when_registering() {
        $response = $this->postJson(route('api.v1.users.register'), []);
        $response->assertStatus(422);
    }

    /**
     * @return void
     */
    public function it_does_not_allow_to_register_existing_email() {
        $email = 'lavoe@gmail.com';
        User::factory()->create(['email' => $email]);

        $response = $this->postJson(route('api.v1.users.register'), array_merge([
            User::factory()->make(['email' => $email ])->toArray(),
            'password' => '123123'
        ]));
        $response->assertStatus(422);
    }
}
