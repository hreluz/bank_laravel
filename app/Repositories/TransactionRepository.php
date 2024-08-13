<?php

namespace App\Repositories;

use App\Http\Resources\Reports\ClientListMonthCollection;
use App\Models\BankAccount;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionRepository
{
    public function clientsFilteredTransactionsByMonth(int $month)
    {
        if ($month < 1 || $month > 12) {
            throw new \Exception('Incorrect month given');
        }

        $query = Transaction::query();

        $transactions = $query
                ->selectRaw('
                    bank_account_id,
                    COUNT(*) as number_transactions,
                    SUM(CASE WHEN type = "ADD" THEN amount ELSE 0 END) -
                    SUM(CASE WHEN type = "SUBTRACT" THEN amount ELSE 0 END) as total_balance')
                ->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])
                ->orderBy('number_transactions', 'DESC')
                ->groupBy('bank_account_id')
                ->get();

        $bank_accounts = BankAccount::with('owner')
            ->whereIn('id', $transactions->pluck('bank_account_id'))
            ->get();

        return new ClientListMonthCollection($transactions, $bank_accounts);
    }
}
