<?php

namespace Database\Factories;

use App\Models\OfferImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfferImage>
 */
class OfferImageFactory extends Factory
{
    protected $model = OfferImage::class;

    public function definition(): array
    {
        return [
            'path' => 'offers/'.fake()->uuid().'.jpg',
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
