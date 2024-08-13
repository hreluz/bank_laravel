<?php

namespace Tests\Feature\Transaction;

use App\Models\Transaction;
use App\Models\User;
use Tests\BaseApiTestCase;
use Tests\Traits\BankAccountTrait;
use Tests\Traits\TransactionStructureTrait;

class TransactionStoreControllerTest extends BaseApiTestCase
{
    use TransactionStructureTrait, BankAccountTrait;

    public function test_user_can_do_an_add_transaction()
    {
        $bank_account = $this->createBankAccountForUser();
        $user = $bank_account->owner;
        $this->authAs($user);

        $this->postJson(route('api.v1.transactions.store', $bank_account->id), [
            'amount' => 100.0,
            'type' => Transaction::TYPE_ADD,
            'city' => 'Lima'
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data'  => [
                    'transaction' => $this->transactionStructure()
                ]
            ])
            ->assertJsonFragment([
                'date' => date('Y-m-d'),
                'amount' => 100,
                'type' => Transaction::TYPE_ADD,
            ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 100,
            'type' => Transaction::TYPE_ADD,
            'bank_account_id' => $bank_account->id,
            'city' => 'Lima'
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 100,
            'company_id' => null,
            'owner_id' => $user->id
        ]);
    }

    public function test_user_can_do_a_subtract_transaction()
    {
        $bank_account = $this->createBankAccountForUser(null, 100);
        $user = $bank_account->owner;
        $this->authAs($user);

        $this->postJson(route('api.v1.transactions.store', $bank_account->id), [
            'amount' => 100.0,
            'type' => Transaction::TYPE_SUBTRACT,
            'city' => 'Madrid'
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data'  => [
                    'transaction' => $this->transactionStructure()
                ]
            ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 100,
            'type' => Transaction::TYPE_SUBTRACT,
            'bank_account_id' => $bank_account->id,
            'city' => 'Madrid'
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 0,
            'company_id' => null,
            'owner_id' => $user->id
        ]);
    }

    public function test_company_can_do_an_add_transaction()
    {
        $bank_account = $this->createBankAccountForCompany();
        $user = $bank_account->company->owner;
        $this->authAs($user);

        $this->postJson(route('api.v1.transactions.store', $bank_account->id), [
            'amount' => 100.0,
            'type' => Transaction::TYPE_ADD,
            'city' => 'Asuncion'
        ])
            ->assertOk()
            ->assertJsonStructure([
            'data'  => [
                'transaction' => $this->transactionStructure()
            ]
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 100,
            'type' => Transaction::TYPE_ADD,
            'bank_account_id' => $bank_account->id,
            'city' => 'Asuncion'
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 100,
            'company_id' => $bank_account->company->id,
            'owner_id' => null
        ]);
    }

    public function test_company_can_do_a_subtract_transaction()
    {
        $bank_account = $this->createBankAccountForCompany(null, 100);
        $user = $bank_account->company->owner;
        $this->authAs($user);

        $this->postJson(route('api.v1.transactions.store', $bank_account->id), [
            'amount' => 100.0,
            'type' => Transaction::TYPE_SUBTRACT,
            'city' => 'Sydney'
        ])
            ->assertOk()
            ->assertJsonStructure([
            'data'  => [
                'transaction' => $this->transactionStructure()
            ]
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => 100,
            'type' => Transaction::TYPE_SUBTRACT,
            'bank_account_id' => $bank_account->id,
            'city' => 'Sydney'
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 0,
            'company_id' => $bank_account->company->id,
            'owner_id' => null
        ]);
    }

    public function test_you_cannot_send_a_negative_amount()
    {
        $bank_account = $this->createBankAccountForCompany(null, 100);
        $user = $bank_account->company->owner;
        $this->authAs($user);

        $this->postJson(route('api.v1.transactions.store', $bank_account->id), [
            'amount' => -100.0,
            'type' => Transaction::TYPE_SUBTRACT,
            'city' => 'Madrid'
        ])->assertUnprocessable();
    }

    public function test_you_cannot_do_a_transaction_if_you_are_not_the_owner()
    {
        $bank_account = $this->createBankAccountForCompany(null, 100);
        $user = User::factory()->create();
        $this->authAs($user);

        $this->postJson(route('api.v1.transactions.store', $bank_account->id), [
            'amount' => 100.0,
            'type' => Transaction::TYPE_SUBTRACT,
            'city' => 'Madrid'
        ])->assertForbidden();
    }

    public function test_you_can_only_send_valid_types_for_transaction()
    {
        $bank_account = $this->createBankAccountForUser(null, 100);
        $this->authAs($bank_account->owner);

        $this->postJson(route('api.v1.transactions.store', $bank_account->id), [
            'amount' => 100.0,
            'type' => 'HELLO',
            'city' => 'Madrid'
        ])->assertUnprocessable();
    }
}
