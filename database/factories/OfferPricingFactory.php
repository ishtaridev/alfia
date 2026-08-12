<?php

namespace Database\Factories;

use App\Models\OfferPricing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfferPricing>
 */
class OfferPricingFactory extends Factory
{
    protected $model = OfferPricing::class;

    public function definition(): array
    {
        return [
            'collectif_room' => fake()->numberBetween(15000, 80000),
            'room_of_four' => fake()->numberBetween(20000, 100000),
            'room_of_three' => fake()->numberBetween(25000, 120000),
            'room_of_two' => fake()->numberBetween(30000, 150000),
            'feeding' => fake()->numberBetween(5000, 20000),
        ];
    }
}
