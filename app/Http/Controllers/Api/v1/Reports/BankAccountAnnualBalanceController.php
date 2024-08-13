<?php

namespace App\Http\Controllers\Api\v1\Reports;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Repositories\BankAccountRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BankAccountAnnualBalanceController extends Controller
{
    public function get(BankAccount $bank_account)
    {
        return (new BankAccountRepository())->annualBalanceByMonth($bank_account);
    }
}
