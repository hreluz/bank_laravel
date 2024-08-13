<?php

namespace Tests\Feature\Transaction;

use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Tests\BaseApiTestCase;
use Tests\Traits\BankAccountTrait;
use Tests\Traits\TransactionStructureTrait;

class ListRecentTransactionsTest extends BaseApiTestCase
{
    use TransactionStructureTrait, BankAccountTrait;

    /**
     * @var TransactionService
     */
    protected TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->transactionService = new TransactionService($this->owner);
        $this->transactionService->setOwner($this->owner);
    }

    public function test_it_list_recent_transactions_of_user()
    {
        $bank_account = $this->createBankAccountForUser($this->owner);
        $this->authAs($this->owner);

        $this->getJson(route('api.v1.transactions.recent', $bank_account->id))
            ->assertOk()
            ->assertJsonCount(0, 'data');

        for ($i = 0 ; $i < 10 ; $i++) {
            $this->transactionService->doTransaction($bank_account,  [
                'type' => Transaction::TYPE_ADD,
                'amount' => rand(1, 800),
                'city' => 'Amsterdam'
            ]);
        }

        sleep(1);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_ADD,
            'amount' => 10,
            'city' => 'Amsterdam'
        ]);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_ADD,
            'amount' => 30,
            'city' => 'Amsterdam'
        ]);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_ADD,
            'amount' => 150,
            'city' => 'Amsterdam'
        ]);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 200,
            'city' => 'Amsterdam'
        ]);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_ADD,
            'amount' => 300,
            'city' => 'Amsterdam'
        ]);

        $this->assertDatabaseCount('transactions', 15);


        $response = $this->getJson(route('api.v1.transactions.recent', $bank_account->id))
                        ->assertOk()
                        ->assertJsonCount(5, 'data');

        $data = $response->decodeResponseJson()['data'];

        $this->assertEquals(300, $data[0]['amount']);
        $this->assertEquals(200, $data[1]['amount']);
        $this->assertEquals(150, $data[2]['amount']);
        $this->assertEquals(30, $data[3]['amount']);
        $this->assertEquals(10, $data[4]['amount']);
    }

    public function test_it_cannot_list_recent_transactions_if_is_not_the_owner()
    {
        $bank_account = $this->createBankAccountForUser($this->owner);
        $not_owner = User::factory()->create();
        $this->authAs($not_owner);

        $this->getJson(route('api.v1.transactions.recent', $bank_account->id))->assertForbidden();
    }
}
