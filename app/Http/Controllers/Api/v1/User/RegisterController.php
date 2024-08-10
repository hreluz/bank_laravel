<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreRegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function store(StoreRegisterRequest $request) {
        $fields = $request->only(['name', 'email', 'password']);

        $user = User::create($fields);

        return response()->json([
            'success'  => true,
            'data'    => [
                'user' => new UserResource($user)
            ],
            'message' => JsonResponse::HTTP_OK,
        ])  ;
    }
}
