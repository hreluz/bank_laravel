<?php

namespace Tests\Traits;

use App\Models\BankAccount;
use App\Models\Company;
use App\Models\User;

trait BankAccountTrait {

    public function createBankAccountForUser(?User $user = null, $balance = 0)
    {
        if ($user == null ) {
            $user = User::factory()->create();
        }

        return BankAccount::factory()->create([
            'company_id' => null,
            'owner_id' => $user->id,
            'balance' => $balance
        ]);
    }

    public function createBankAccountForCompany(?Company $company = null, $balance = 0)
    {
        if ($company == null ) {
            $company = Company::factory()->create();
        }

        return BankAccount::factory()->create([
            'owner_id' => null,
            'company_id' => $company->id,
            'balance' => $balance
        ]);
    }
}
