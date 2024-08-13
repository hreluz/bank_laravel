<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

use Illuminate\Support\Collection;

class ClientListMonthCollection extends ResourceCollection
{
    private Collection $bank_accounts;

    public function __construct($resource, Collection $bank_accounts)
    {
        parent::__construct($resource);
        $this->bank_accounts = $bank_accounts;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        foreach ($this->collection as $transaction) {

            $data[] = [
                'total_balance' => $transaction->total_balance,
                'number_transactions' => $transaction->number_transactions,
                'owner' => UserResource::make($this->bank_accounts->find($transaction->bank_account_id)->owner)
            ];
        }
        return $data;
    }
}
