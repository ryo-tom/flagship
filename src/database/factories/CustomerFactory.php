<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'          => $this->faker->company,
            'name_kana'     => $this->faker->name,
            'shortcut'      => $this->faker->word,
            'postal_code'   => $this->faker->postcode,
            'address'       => $this->faker->address,
            'tel_number'    => $this->faker->phoneNumber,
            'fax_number'    => $this->faker->phoneNumber,
            'note'          => $this->faker->sentence,
            'in_charge_user_id' => User::inRandomOrder()->first()->id,
            'created_by_id'     => User::inRandomOrder()->first()->id,
            'updated_by_id'     => User::inRandomOrder()->first()->id,
        ];
    }
}
