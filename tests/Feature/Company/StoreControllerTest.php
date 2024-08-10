<?php

namespace Tests\Feature\Company;

use App\Models\User;
use Tests\BaseApiTestCase;

class StoreControllerTest extends BaseApiTestCase
{
    use CompanyStructureTrait;

    /**
     * @return void
     */
    public function test_a_user_can_register_a_company()
    {
        $user = User::factory()->create();
        $this->authAs($user);
        $company_name = 'Company Name';

        $response = $this->postJson(route('api.v1.companies.store'), [
            'name' => $company_name
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data'  => [
                'company' => $this->companyStructure()
            ]
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => $company_name,
            'owner_id' => $user->id
        ]);
    }
}
