<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TransactionService
{
    private ?User $owner;

    /**
     * @param User|null $owner
     */
    public function __construct(?User $owner = null)
    {
        $this->owner = $owner;
    }

    /**
     * @param User $owner
     * @return void
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @param BankAccount $bank_account
     * @param array $fields
     * @return mixed
     * @throws \Exception
     */
    public function doTransaction(BankAccount $bank_account, array $fields)
    {
        if(empty($fields['amount']) || empty($fields['type']) || empty($fields['city'])) {
            throw new \Exception('fields amount, type and city are required');
        }

        $amount = $fields['amount'];
        $type = $fields['type'];
        $city = $fields['city'];

        if (!Gate::forUser($this->owner)->allows('isOwner', [Transaction::class, $bank_account]) || $amount <= 0) {
            abort(403, 'Unauthorized action.');
        }

        return DB::transaction(function () use ($bank_account, $type, $amount, $city) {

            $locked_bank_account = BankAccount::where('id', $bank_account->id)->lockForUpdate()->first();

            if ($type === Transaction::TYPE_SUBTRACT && $locked_bank_account->balance < $amount) {
                return [false, null];
            }

            $transaction = $locked_bank_account->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'city' => $city
            ]);

            $locked_bank_account->addToBalance($amount, $type);
            $locked_bank_account->save();

            return [true, $transaction];
        });
    }
}
