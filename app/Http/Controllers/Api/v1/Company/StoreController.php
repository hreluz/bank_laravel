<?php

namespace App\Http\Controllers\Api\v1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Company\StoreRequest;
use App\Http\Resources\Company\CompanyResource;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function store(StoreRequest $request)
    {
        $fields = $request->only(['name']);

        $company  = auth()->user()->companies()->create($fields);

        return response()->json([
            'success'  => true,
            'data'    => [
                'company' => new CompanyResource($company)
            ],
            'message' => JsonResponse::HTTP_OK,
        ]);
    }
}
