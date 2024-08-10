<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'        => 'v1' ,
    'namespace'     => 'Api\v1',
    'as'            => 'api.v1.'
], function() {

//    Route::post('login', [LoginController::class, 'login'])
//        ->name('login');

    Route::post('register', [\App\Http\Controllers\Api\v1\User\RegisterController::class, 'store'])
        ->name('users.register');
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
