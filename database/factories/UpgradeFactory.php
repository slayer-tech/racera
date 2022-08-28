<?php

namespace Database\Factories;

use App\Models\Bonus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Upgrade>
 */
class UpgradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'price' => $this->faker->numberBetween(1, 10000),
            'bonus_id' => Bonus::factory()->create()->id
        ];
    }
}
