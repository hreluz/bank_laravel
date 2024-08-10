<?php

use App\Http\Controllers\Api\v1\Auth\{LoginController, RegisterController};
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'        => 'v1' ,
    'namespace'     => 'Api\v1',
    'as'            => 'api.v1.'
], function() {

    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('register', [RegisterController::class, 'store'])->name('users.register');

    Route::group(['middleware' => ['auth:sanctum']], function(){
        Route::get('/authenticated', fn () => 'You are authenticated')->name('auth.authenticated');

        Route::name('companies.')
            ->group(function () {

                Route::post('', [\App\Http\Controllers\Api\v1\Company\StoreController::class, 'store'])
                    ->name('store');
            });

        Route::name('bank_accounts.')
            ->group(function () {

                Route::post('', [\App\Http\Controllers\Api\v1\BankAccount\StoreController::class, 'store'])
                    ->name('store');
            });
    });
});

