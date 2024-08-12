<?php

namespace App\Policies;

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
}
