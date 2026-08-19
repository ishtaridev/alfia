<?php

use App\Models\Offer;
use App\Models\OfferVariant;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\ReservationCreated;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->actingAs($this->user);
});

test('admin can create a reservation for an offer variant', function () {
    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()->for($offer)->create();

    Notification::fake();

    $response = $this->post(route('offer-variants.reservations.store', $variant->id), [
        'customer' => 'John Doe',
        'phone' => '123456789',
        'travellers_number' => 2,
        'wilaya' => 'Alger',
        'room_type' => 'collectif',
        'status' => 'pending',
        'include_feeding' => true,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('reservations', [
        'variant_id' => $variant->id,
        'customer' => 'John Doe',
        'room_type' => 'collectif',
    ]);

    Notification::assertSentOnDemand(ReservationCreated::class);
});

test('reservation belongs to variant and variant has reservations', function () {
    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()->for($offer)->create();
    $reservation = Reservation::factory()->for($variant, 'variant')->create();

    $this->assertEquals($variant->id, $reservation->variant->id);
    $this->assertTrue($variant->reservations->contains($reservation));
});

test('admin can delete a reservation', function () {
    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()->for($offer)->create();
    $reservation = Reservation::factory()->for($variant, 'variant')->create();

    $response = $this->delete(route('offer-variants.reservations.destroy', [$variant->id, $reservation->id]));

    $response->assertRedirect();
    $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
});
