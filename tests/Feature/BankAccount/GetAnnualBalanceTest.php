<?php

namespace Tests\Feature\BankAccount;

use App\Models\BankAccount;
use App\Models\Transaction;
use Carbon\Carbon;
use Tests\BaseApiTestCase;

class GetAnnualBalanceTest extends BaseApiTestCase
{
    public function test_owner_can_get_annual_balance()
    {
        $bank_account = BankAccount::factory()->create([
            'balance' => 0,
            'company_id' => null
        ]);

        $start_of_year = Carbon::now()->startOfYear();
        $this->addTransaction($bank_account, $start_of_year, 100 );

        for ($i = 2 ; $i <= date('m'); $i++) {
            $created_at = (Carbon::now()->startOfYear())->addMonths($i-1);
            $this->addTransaction($bank_account, $created_at, 100 * $i);
        }

        $this->authAs($bank_account->owner);
        $response = $this->getJson(route('api.v1.reports.bank_accounts.balance.annual', $bank_account->id))->assertOk();

        for ($i = 1 ; $i <= date('m'); $i++) {
            $response->assertJsonFragment([
                'total_balance' => 100*$i,
                'month' => $i
            ]);
        }
    }

    /**
     * @param BankAccount $bank_account
     * @param $created_at
     * @param $amount
     * @return void
     */
    public function addTransaction(BankAccount $bank_account, $created_at, $amount): void
    {
        $t = Transaction::factory()->create([
            'bank_account_id' => $bank_account->id,
            'type' => Transaction::TYPE_ADD,
            'amount' => $amount
        ]);
        $t->created_at = $created_at;
        $t->save();
    }
}
