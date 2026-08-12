<?php

namespace Database\Factories;

use App\Models\OfferVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfferVariant>
 */
class OfferVariantFactory extends Factory
{
    protected $model = OfferVariant::class;

    public function definition(): array
    {
        return [
            'travel_date' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'airport' => fake()->randomElement(['Algiers', 'Oran', 'Constantine', 'Annaba', 'Tlemcen']),
        ];
    }
}
