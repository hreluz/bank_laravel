<?php

namespace App\Http\Resources\BankAccount;

use App\Http\Resources\Company\CompanyResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id,
            'balance' => $this->balance,
            'owner' => $this->owner ? new UserResource($this->owner) : [],
            'company' => $this->company ? new CompanyResource($this->company) : [],
        ];
    }
}
