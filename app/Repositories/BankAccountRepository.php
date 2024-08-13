<?php

namespace App\Repositories;

use App\Http\Resources\Reports\BankAccountAnnualBalanceCollection;
use App\Models\BankAccount;
use App\Models\Transaction;

class BankAccountRepository
{
    /**
     * @param BankAccount $bank_account
     * @return BankAccountAnnualBalanceCollection
     */
    public function annualBalanceByMonth(BankAccount $bank_account): BankAccountAnnualBalanceCollection
    {
        $query = Transaction::query();

        $transactions = $query
            ->selectRaw('
                    MONTH(created_at) as month,
                    SUM(CASE WHEN type = "ADD" THEN amount ELSE 0 END) -
                    SUM(CASE WHEN type = "SUBTRACT" THEN amount ELSE 0 END) as total_balance')
            ->where('bank_account_id', $bank_account->id)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return new BankAccountAnnualBalanceCollection($transactions);
    }
}
