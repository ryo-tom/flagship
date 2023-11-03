<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerContact>
 */
class CustomerContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id'   => Customer::inRandomOrder()->first()->id,
            'contact_name'  => $this->faker->name,
            'tel_number'    => $this->faker->phoneNumber,
            'fax_number'    => $this->faker->phoneNumber,
            'mobile_number' => $this->faker->phoneNumber,
            'email'         => $this->faker->safeEmail,
            'position'      => $this->faker->jobTitle,
            'role'          => $this->faker->word,
            'is_active'     => $this->faker->boolean,
            'note'          => $this->faker->sentence,
            'in_charge_user_id' => User::inRandomOrder()->first()->id,
            'created_by_id'     => User::inRandomOrder()->first()->id,
            'updated_by_id'     => User::inRandomOrder()->first()->id,
        ];
    }
}
