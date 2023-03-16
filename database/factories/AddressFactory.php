<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
            'name' => fake()->lastName(),
            'firstname' => fake()->firstName(),
            'street' => fake()->streetAddress(),
            'lat' => fake()->latitude(47.34,47.41),
            'lng' => fake()->longitude(8.47,8.55),
            'plz' => '8000',
            'city' => 'Zürich',
            'center' => 'false',

        ];
    }
}
