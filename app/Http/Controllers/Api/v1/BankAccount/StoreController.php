<?php

namespace App\Http\Controllers\Api\v1\BankAccount;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BankAccount\StoreRequest;
use App\Http\Resources\BankAccount\BankAccountResource;
use App\Services\BankAccountService;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    /**
     * @param StoreRequest $request
     * @return mixed
     */
    public function store(StoreRequest $request)
    {
        $fields = $request->only(['company_id']);

        $company_id = !empty($fields['company_id']) ? $fields['company_id'] : null;

        $bank_account = (new  BankAccountService)->createAccount(auth()->user(), $company_id);

        return response()->json([
            'success'  => true,
            'data'    => [
                'bank_account' => new BankAccountResource($bank_account)
            ],
            'message' => JsonResponse::HTTP_OK,
        ]);
    }
}
