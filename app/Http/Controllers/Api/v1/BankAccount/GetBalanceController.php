<?php

namespace App\Http\Controllers\Api\v1\BankAccount;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankAccount\BankAccountResource;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GetBalanceController extends Controller
{
    public function get_balance(BankAccount $bank_account)
    {
        if (!Gate::allows('isOwner', [BankAccount::class, $bank_account])) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json([
            'success'  => true,
            'data'    => [
                'bank_account' => new BankAccountResource($bank_account)
            ],
            'message' => JsonResponse::HTTP_OK,
        ]);
    }
}
