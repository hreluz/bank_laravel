<?php

use App\Http\Controllers\Api\v1\Reports\TransactionsReportController;
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
            ->prefix('companies')
            ->group(function () {

                Route::post('', [\App\Http\Controllers\Api\v1\Company\StoreController::class, 'store'])
                    ->name('store');
            });

        Route::name('bank_accounts.')
            ->prefix('bank_accounts')
            ->group(function () {

                Route::post('', [\App\Http\Controllers\Api\v1\BankAccount\StoreController::class, 'store'])
                    ->name('store');
            });

        Route::name('transactions.')
            ->prefix('transactions')
            ->group(function () {

                Route::post('{bank_account}', [\App\Http\Controllers\Api\v1\Transactions\StoreController::class, 'store'])
                    ->name('store');

                Route::get('recent/{bank_account}', [\App\Http\Controllers\Api\v1\Transactions\ListRecentController::class, 'list_recent'])
                    ->name('recent');
            });

        Route::name('reports.')
            ->prefix('reports')
            ->group(function () {

                Route::get('clients-filtered-transactions-by-month', [TransactionsReportController::class, 'clientsFilteredTransactionsByMonth'])
                    ->name('clients.filtered.transactions.by.month');

                Route::get('transactions-filtered-by-10k-and-city', [TransactionsReportController::class, 'transactionsFilteredBy10kAndCity'])
                    ->name(  'transactions.filtered.by.10k.and.city');
            });
    });
});

