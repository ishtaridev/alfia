<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'code' => Offer::generateUniqueCode(),
            'description' => fake()->paragraph(),
        ];
    }
}
