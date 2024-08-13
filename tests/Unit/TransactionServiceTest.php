<?php

namespace Tests\Unit;

use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;
use Tests\Traits\BankAccountTrait;
use Illuminate\Support\Facades\DB;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase ,BankAccountTrait;

    /**
     * @var TransactionService
     */
    protected TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->transactionService = new TransactionService($this->owner);
    }

    public function test_it_can_add_amount_for_user()
    {
        $bank_account = $this->createBankAccountForUser($this->owner);
        $this->transactionService->doTransaction($bank_account, [
            'type' => Transaction::TYPE_ADD,
            'amount' => 100,
            'city' => 'Madrid'
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseHas('transactions', [
            'amount' => 100,
            'type' => Transaction::TYPE_ADD,
            'bank_account_id' => $bank_account->id
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 100,
            'company_id' => null
        ]);
    }

    public function test_it_can_subtract_amount_for_user()
    {
        $bank_account = $this->createBankAccountForUser($this->owner, 100);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 100,
            'city' => 'Barcelona'
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseHas('transactions', [
            'amount' => 100,
            'type' => Transaction::TYPE_SUBTRACT,
            'bank_account_id' => $bank_account->id
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 0
        ]);
    }

    public function test_it_cannot_subtract_amount_if_amount_is_higher_than_balance_for_user()
    {
        $bank_account = $this->createBankAccountForUser($this->owner, 20);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 100,
            'city' => 'Hong Kong'
        ]);

        $this->assertDatabaseCount('transactions', 0);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 20,
            'company_id' => null
        ]);
    }

    public function test_it_can_add_amount_for_company()
    {
        $company = Company::factory()->create(['owner_id' => $this->owner->id]);
        $bank_account = $this->createBankAccountForCompany($company);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_ADD,
            'amount' => 100,
            'city' => 'Tokyo'
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseHas('transactions', [
            'amount' => 100,
            'type' => Transaction::TYPE_ADD,
            'bank_account_id' => $bank_account->id
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 100,
            'owner_id' => null,
        ]);
    }

    public function test_it_can_subtract_amount_for_company()
    {
        $company = Company::factory()->create(['owner_id' => $this->owner->id]);
        $bank_account = $this->createBankAccountForCompany($company, 100);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 100,
            'city' => 'Nairobi'
        ]);

        $this->assertDatabaseCount('transactions', 1);

        $this->assertDatabaseHas('transactions', [
            'amount' => 100,
            'type' => Transaction::TYPE_SUBTRACT,
            'bank_account_id' => $bank_account->id
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 0,
            'owner_id' => null
        ]);
    }

    public function test_it_cannot_subtract_amount_if_amount_is_higher_than_balance_for_company()
    {
        $company = Company::factory()->create(['owner_id' => $this->owner->id]);
        $bank_account = $this->createBankAccountForCompany($company, 20);

        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 100,
            'city' => 'San Juan'
        ]);

        $this->assertDatabaseCount('transactions', 0);

        $this->assertDatabaseHas('bank_accounts', [
            'id' => $bank_account->id,
            'balance' => 20,
            'owner_id' => null
        ]);
    }

    public function test_it_cannot_do_a_transaction_if_bank_account_owner_is_not_the_same()
    {
        $notOwner = User::factory()->create();
        $bank_account = $this->createBankAccountForUser($this->owner);
        $this->transactionService->setOwner($notOwner);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized action.');
        $this->transactionService->doTransaction($bank_account,  [
            'type' => Transaction::TYPE_ADD,
            'amount' => 100,
            'city' => 'Paris'
        ]);
    }

    public function test_multiple_transactions()
    {
        $bank_account = $this->createBankAccountForUser($this->owner, 1000);
        $bank_account_id  = $bank_account->id;

        DB::beginTransaction();
        $_bank_account = BankAccount::where('id', $bank_account_id)->lockForUpdate()->first();
        $this->transactionService->doTransaction($_bank_account,  [
            'type' => Transaction::TYPE_SUBTRACT,
            'amount' => 500,
            'city' => 'Amsterdam'
        ]);
        $otherTransactionExecuted = false;

        DB::transaction(function () use ($bank_account_id, &$otherTransactionExecuted) {
            $otherTransactionExecuted = true;
            $__bank_account = BankAccount::where('id', $bank_account_id)->lockForUpdate()->first();
            $this->transactionService->doTransaction($__bank_account,  [
                'type' => Transaction::TYPE_SUBTRACT,
                'amount' => 600,
                'city' => 'Madrid'
            ]);
            $this->transactionService->doTransaction($__bank_account,  [
                'type' => Transaction::TYPE_SUBTRACT,
                'amount' => 300,
                'city' => 'Quito'
            ]);
        });

        DB::commit();

        $bank_account->refresh();
        $this->assertEquals(200, $bank_account->balance);
        $this->assertTrue($otherTransactionExecuted);
    }
}
