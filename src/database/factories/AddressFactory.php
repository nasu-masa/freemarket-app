<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition()
    {
        return [
            'postal_code' => $this->faker->postcode(),
            'address'     => $this->faker->prefecture()
                            . $this->faker->city()
                            . $this->faker->streetAddress(),
            'building'    => $this->faker->secondaryAddress(),
        ];
    }
}
