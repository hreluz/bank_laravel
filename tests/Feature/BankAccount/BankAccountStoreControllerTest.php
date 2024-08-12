<?php

namespace Tests\Feature\BankAccount;

use App\Models\Company;
use App\Models\User;
use App\Services\BankAccountService;
use Tests\BaseApiTestCase;
use Tests\Traits\BankAccountStructureTrait;

class BankAccountStoreControllerTest extends BaseApiTestCase
{
    use BankAccountStructureTrait;

    public function test_a_user_can_open_a_bank_account()
    {
        $user = User::factory()->create();
        $this->authAs($user);

        $response = $this->postJson(route('api.v1.bank_accounts.store'), [
            'owner_id' => $user->id
        ])
            ->assertOk();

        $response->assertJsonStructure([
            'data'  => [
                'bank_account' => $this->bankAccountStructure()
            ]
        ]);
    }

    public function test_a_user_cannot_open_more_than_one_bank_account()
    {
        $user = User::factory()->create();

        (new BankAccountService())->createAccount($user);

        $this->assertDatabaseCount('bank_accounts', 1);

        $this->authAs($user);

        $this->postJson(route('api.v1.bank_accounts.store'), [
            'owner_id' => $user->id
        ])->assertForbidden();
    }


    public function test_a_user_can_open_a_bank_account_for_a_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);
        $this->authAs($user);

        $this->postJson(route('api.v1.bank_accounts.store'), [
            'company_id' => $company->id
        ])
            ->assertOk()
            ->assertJsonStructure([
            'data'  => [
                'bank_account' => $this->bankAccountStructure()
            ]
        ]);
    }

    public function test_a_user_cannot_open_more_than_one_bank_account_for_a_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);
        $this->authAs($user);

        (new BankAccountService())->createAccount($user, $company->id);

        $this->postJson(route('api.v1.bank_accounts.store'), [
            'company_id' => $company->id
        ])->assertForbidden();
    }
}
