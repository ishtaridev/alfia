<?php

namespace Database\Factories;

use App\Models\Mentor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mentor>
 */
class MentorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'phone' => '05'.$this->faker->unique()->numerify('########'),
            'commune' => $this->faker->randomElement(config('communes.bordj_bou_arreridj')),
        ];
    }
}
