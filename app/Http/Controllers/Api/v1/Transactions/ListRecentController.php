<?php

namespace App\Http\Controllers\Api\v1\Transactions;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Gate;

class ListRecentController extends Controller
{
    /**
     * @param BankAccount $bank_account
     * @return mixed
     */
    public function list_recent(BankAccount $bank_account)
    {
        if (!Gate::allows('isOwner', [BankAccount::class, $bank_account])) {
            abort(403, 'Unauthorized action.');
        }

        return (new TransactionRepository())->recentTransactions($bank_account);
    }
}
