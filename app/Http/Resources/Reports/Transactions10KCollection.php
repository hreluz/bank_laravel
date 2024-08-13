<?php

namespace App\Http\Resources\Reports;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class Transactions10KCollection extends ResourceCollection
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
            $bank_account = $this->bank_accounts->find($transaction->bank_account_id);
            $data[] = [
                'bank_account_id' => $transaction->bank_account_id,
                'total_withdrawal' => $transaction->total_withdrawal,
                'origin_city' => $bank_account->city,
                'owner' => UserResource::make($bank_account->owner)
            ];
        }
        return $data;
    }
}
