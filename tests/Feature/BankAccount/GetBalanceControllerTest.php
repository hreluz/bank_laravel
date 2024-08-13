<?php

namespace Tests\Feature\BankAccount;

use App\Models\BankAccount;
use App\Models\User;
use Tests\BaseApiTestCase;

class GetBalanceControllerTest extends BaseApiTestCase
{
    public function test_it_can_get_bank_account_balance()
    {
        $bank_account = BankAccount::factory()->create([
            'balance' => 1500,
            'company_id' => null
        ]);

        $this->authAs($bank_account->owner);

        $this->getJson(route('api.v1.bank_accounts.balance', $bank_account->id))
            ->assertOk()
            ->assertJsonFragment([
                'balance' => 1500,
                'id' => $bank_account->id
            ]);
    }
    public function test_it_cannot_get_balance_if_it_is_not_the_owner()
    {
        $bank_account = BankAccount::factory()->create([
            'balance' => 1500,
            'company_id' => null
        ]);

        $not_owner = User::factory()->create();
        $this->authAs($not_owner);

        $this->getJson(route('api.v1.bank_accounts.balance', $bank_account->id))
            ->assertForbidden();
    }
}
