<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LogisticsAddress>
 */
class LogisticsAddressFactory extends Factory
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
            'address_type'  => $this->faker->randomElement([1, 2, 3]),
            'post_code'     => $this->faker->postcode,
            'address'       => $this->faker->address,
            'company_name'  => $this->faker->company,
            'contact_name'  => $this->faker->name,
            'tel'           => $this->faker->phoneNumber,
            'note'          => $this->faker->text(200)
        ];
    }
}
