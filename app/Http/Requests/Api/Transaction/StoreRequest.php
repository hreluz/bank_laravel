<?php

namespace App\Http\Requests\Api\Transaction;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && Gate::allows('isOwner', [Transaction::class, $this->bank_account]);;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required','numeric','min:0','not_in:0'],
            'city' => ['required'],
            'type' => [
                Rule::in([Transaction::TYPE_ADD, Transaction::TYPE_SUBTRACT]),
            ]
        ];
    }
}
