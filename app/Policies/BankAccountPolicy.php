<?php

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\User;

class BankAccountPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param User $user
     * @param $company_id
     * @return bool
     */
    public function create(User $user, $company_id = null): bool
    {
        if ($company_id != null) {

            $company = $user->companies()->where('id', $company_id)->first();

            return $company->bank_account()->exists() == false;
        }

        return $user->bank_account()->exists() == false;
    }

    /**
     * @param User $user
     * @param BankAccount $bank_account
     * @return bool
     */
    public function isOwner(User $user, BankAccount $bank_account): bool
    {
        if ($bank_account->owner_id > 0) {
            return $bank_account->owner_id == $user->id;
        }

        return $bank_account->company->owner->id == $user->id;
    }
}
