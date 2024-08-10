<?php

namespace Tests\Feature\Company;

use App\Models\User;
use Tests\BaseApiTestCase;

class CompanyStoreControllerTest extends BaseApiTestCase
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

        $this->postJson(route('api.v1.companies.store'), [
            'name' => $company_name
        ])
            ->assertOk()
            ->assertJsonStructure([
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
