<?php

namespace App\Http\Controllers\Api\v1\Reports;

use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;

class TransactionsReportController extends Controller
{
    public function clientsFilteredTransactionsByMonth(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'numeric']
        ]);

        return (new TransactionRepository())->clientsFilteredTransactionsByMonth($validated['month']);
    }
}
