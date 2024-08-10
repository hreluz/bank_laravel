<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    use Authenticatable;

    public function login(LoginRequest $request)
    {
        $data = $request->only(['email', 'password']);

        if (auth()->attempt($data)) {
            return $this->successLogin();
        }

        return response()->json([
            'success'      => false,
            'status_code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            'errors'      => [
                'email' => 'Authentication Failed'
            ],
            'message'     => 'There are some errors',
        ]);
    }

    public function successLogin() {
        $user = auth()->user();
        $authToken = $user->createToken('authToken', ['*'], Carbon::now()->addMinutes(config('sanctum.expiration')));

        return response()->json([
            'success'  => true,
            'data'    => [
                'access_token'       => $authToken->accessToken,
                'expires_in_seconds' => $authToken->accessToken->expires_at->diffInSeconds(Carbon::now()),
                'user'              => new UserResource($user)
            ],
            'message' => JsonResponse::HTTP_OK,
        ]);
    }
}
