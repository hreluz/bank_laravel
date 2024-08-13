<?php

namespace Tests\Feature\Reports;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Carbon\Carbon;
use Tests\BaseApiTestCase;

class TransactionReportControllerTest extends BaseApiTestCase
{
    protected TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->transactionService = new TransactionService($this->owner);
    }

    public function test_report_transactions_by_month()
    {
        $bank_accounts = BankAccount::factory()->count(100)->create([
            'company_id' => null,
            'balance' => 0
        ]);

        $n1 = $bank_accounts->first()->id;

        $bank_account1 = BankAccount::find($n1+ 70);
        $bank_account2 = BankAccount::find($n1 + 30);
        $bank_account3 = BankAccount::find($n1 +10);
        $bank_account4 = BankAccount::find($n1 + 26);
        $bank_account5 = BankAccount::find($n1 + 89);

        $this->transactionService->setOwner($bank_account1->owner);
        $this->transactionService->doTransaction($bank_account1, Transaction::TYPE_ADD, 500);
        $this->transactionService->doTransaction($bank_account1, Transaction::TYPE_ADD, 1000);

        $this->transactionService->setOwner($bank_account2->owner);
        $this->transactionService->doTransaction($bank_account2, Transaction::TYPE_ADD, 99);

        $this->transactionService->setOwner($bank_account1->owner);
        $this->transactionService->doTransaction($bank_account1, Transaction::TYPE_SUBTRACT, 30);

        $this->transactionService->setOwner($bank_account3->owner);
        $this->transactionService->doTransaction($bank_account3, Transaction::TYPE_ADD, 72);

        $this->transactionService->setOwner($bank_account4->owner);
        $this->transactionService->doTransaction($bank_account4, Transaction::TYPE_ADD, 67000);

        $this->transactionService->setOwner($bank_account2->owner);
        $this->transactionService->doTransaction($bank_account2, Transaction::TYPE_ADD, 1);
        $this->transactionService->doTransaction($bank_account2, Transaction::TYPE_SUBTRACT, 19);
        $this->transactionService->doTransaction($bank_account2, Transaction::TYPE_ADD, 80);

        $this->transactionService->setOwner($bank_account4->owner);
        $this->transactionService->doTransaction($bank_account4, Transaction::TYPE_ADD, 33);

        $this->transactionService->setOwner($bank_account5->owner);
        $this->transactionService->doTransaction($bank_account5, Transaction::TYPE_ADD, 23);

        // Add a transaction from one month ago
        $t = Transaction::factory()->create();
        $t->created_at = Carbon::now()->subMonths(1);
        $t->save();

        $bank_account_balance_1 = 1470;
        $bank_account_balance_2 = 161;
        $bank_account_balance_3 = 72;
        $bank_account_balance_4 = 67033;
        $bank_account_balance_5 = 23;

        $this->assertEquals($bank_account_balance_1, $bank_account1->refresh()->balance);
        $this->assertEquals($bank_account_balance_2, $bank_account2->refresh()->balance);
        $this->assertEquals($bank_account_balance_3, $bank_account3->refresh()->balance);
        $this->assertEquals($bank_account_balance_4, $bank_account4->refresh()->balance);
        $this->assertEquals($bank_account_balance_5, $bank_account5->refresh()->balance);

        $user = User::factory()->create();
        $this->authAs($user);

        $response = $this->getJson(route('api.v1.reports.clients.filtered.transactions.by.month', [
            'month' => date('m')
        ]))->assertOk();

        $data = $response->decodeResponseJson()['data'];

        $this->assertEquals($bank_account_balance_2, $data[0]['total_balance']);
        $this->assertEquals($bank_account_balance_1, $data[1]['total_balance']);
        $this->assertEquals($bank_account_balance_4, $data[2]['total_balance']);
        $this->assertEquals($bank_account_balance_3, $data[3]['total_balance']);
        $this->assertEquals($bank_account_balance_5, $data[4]['total_balance']);

        $this->assertEquals($bank_account2->owner->id, $data[0]['owner']['id']);
        $this->assertEquals($bank_account1->owner->id, $data[1]['owner']['id']);
        $this->assertEquals($bank_account4->owner->id, $data[2]['owner']['id']);
        $this->assertEquals($bank_account3->owner->id, $data[3]['owner']['id']);
        $this->assertEquals($bank_account5->owner->id, $data[4]['owner']['id']);

        $response->assertJsonCount(5, 'data');
    }

    public function test_list_transactions_needs_the_month()
    {
        $this->authAs();
        $this->getJson(route('api.v1.reports.clients.filtered.transactions.by.month'))->assertUnprocessable();
    }
}
