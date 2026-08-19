<?php

namespace Database\Factories;

use App\Enums\RoomType;
use App\Models\OfferVariant;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'variant_id' => OfferVariant::factory(),
            'customer' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'travellers_number' => $this->faker->numberBetween(1, 10),
            'wilaya' => $this->faker->randomElement(config('wilayas')),
            'room_type' => $this->faker->randomElement(RoomType::cases()),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'include_feeding' => $this->faker->boolean(),
            'total_price' => $this->faker->numberBetween(100, 1000),
        ];
    }
}
