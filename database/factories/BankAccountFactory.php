<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankAccount>
 */
class BankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'balance' => rand(1,1000),
            'city' => fake()->city(),
            'owner_id' =>   fn() => User::factory()->create()->id,
            'company_id' =>   fn() => Company::factory()->create()->id,
        ];
    }
}
