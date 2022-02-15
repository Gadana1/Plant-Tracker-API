<?php

namespace Database\Factories\v1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\v1\Plant>
 */
class PlantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'species' => $this->faker->word(),
            'instructions' => $this->faker->sentence(40),
            'image' => "https://picsum.photos/800/600?random=".rand(10, 10000)
        ];
    }
}
