<?php

namespace Tests\Feature\Reports;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Tests\BaseApiTestCase;

class TransactionReportByAmountCityNotOriginTest extends BaseApiTestCase
{
    protected TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->transactionService = new TransactionService($this->owner);
    }

    public function test_it_generates_report_by_amount_and_where_city_is_not_origin()
    {
        $bank_account1 = BankAccount::factory()->create([
            'company_id' => null,
            'balance' => 45000,
            'city' => 'New York'
        ]);

        $bank_account2 = BankAccount::factory()->create([
            'company_id' => null,
            'balance' => 32000,
            'city' => 'Madrid'
        ]);

        $bank_account3 = BankAccount::factory()->create([
            'company_id' => null,
            'balance' => 25000,
            'city' => 'Madrid'
        ]);

        $this->transactionService->setOwner($bank_account1->owner);
        $this->transactionService->doTransaction($bank_account1, [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 12000,
            'city' => 'Milan'
        ]);

        $this->transactionService->doTransaction($bank_account1, [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 16000,
            'city' => 'Moscu'
        ]);

        $this->transactionService->setOwner($bank_account2->owner);
        $this->transactionService->doTransaction($bank_account2, [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 10500,
            'city' => 'Montevideo'
        ]);

        $this->transactionService->setOwner($bank_account3->owner);
        $this->transactionService->doTransaction($bank_account3, [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 15000,
            'city' => 'Madrid'
        ]);

        $user = User::factory()->create();

        $this->authAs($user);

        $response = $this->getJson(route('api.v1.reports.transactions.filtered.by.10k.and.city'))->assertOk();

        $response->decodeResponseJson()['data'];

        $response->assertJsonFragment([
            'total_withdrawal' => "28000",
            'bank_account_id' => $bank_account1->id
        ]);

        $response->assertJsonFragment([
            'total_withdrawal' => "10500",
            'bank_account_id' => $bank_account2->id
        ]);

        $response->assertJsonCount(2, 'data');
    }
}
