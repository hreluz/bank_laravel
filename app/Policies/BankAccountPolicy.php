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
     * Determine if the given post can be updated by the user.
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
