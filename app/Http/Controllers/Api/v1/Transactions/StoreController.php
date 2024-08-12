<?php

namespace App\Http\Controllers\Api\v1\Transactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Transaction\StoreRequest;
use App\Http\Resources\Transaction\TransactionResource;
use App\Models\BankAccount;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function store(StoreRequest $request, BankAccount $bank_account)
    {
        $fields = $request->only(['amount', 'type']);

        [$success, $transaction] = (new TransactionService(auth()->user()))
                                        ->doTransaction($bank_account,  $fields['type'], $fields['amount']);

        return response()->json([
            'success'  => $success,
            'data'    => [
                'transaction' => $success ? new TransactionResource($transaction) : []
            ],
            'message' => JsonResponse::HTTP_OK,
        ]);
    }
}
