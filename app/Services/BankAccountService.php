<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class BankAccountService
{
    /**
     * @param User $owner
     * @param string $city
     * @param null $company_id
     * @return BankAccount
     */
    public function createAccount(User $owner, string $city, $company_id = null): BankAccount
    {
        if (!Gate::forUser($owner)->allows('create', [BankAccount::class, $company_id])) {
            abort(403, 'Unauthorized action.');
        }

        if ($company_id != null) {
            $query =  $owner->companies()->find($company_id)?->bank_account();
        } else {
            $query = $owner->bank_account();
        }

        return $query->create(['city' => $city]);
    }
}
