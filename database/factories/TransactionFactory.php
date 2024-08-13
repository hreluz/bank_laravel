<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => rand(1, 1000),
            'city' => fake()->city(),
            'type' => fake()->randomElement([Transaction::TYPE_ADD, Transaction::TYPE_SUBTRACT]),
            'bank_account_id' => fn() => BankAccount::factory()->create()->id
        ];
    }
}
