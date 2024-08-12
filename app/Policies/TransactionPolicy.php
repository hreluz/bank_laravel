<?php

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\User;

class TransactionPolicy
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
     * @param BankAccount $bankAccount
     * @return bool
     */
    public function isOwner(User $user, BankAccount $bankAccount)
    {
        if ($bankAccount->company != null ) {
            return $user->id === $bankAccount->company->owner->id;
        }

        return $user->id == $bankAccount->owner->id;
    }
}
