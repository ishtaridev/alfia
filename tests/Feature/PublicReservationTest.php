<?php

use App\Models\Offer;
use App\Models\OfferPricing;
use App\Models\OfferVariant;
use App\Models\Reservation;
use App\Notifications\ReservationCreated;
use Illuminate\Support\Facades\Notification;

test('anyone can view the public reservation page for an offer', function () {
    $offer = Offer::factory()->create();
    OfferVariant::factory()
        ->for($offer)
        ->has(OfferPricing::factory(), 'pricing')
        ->count(2)
        ->create();

    $response = $this->get(route('offers.reserve', $offer));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('offers/Reserve')
        ->has('offer')
    );
});

test('guest can create a reservation without authentication', function () {
    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()
        ->for($offer)
        ->has(OfferPricing::factory()->state([
            'collectif_room' => 10000,
            'feeding' => 2000,
        ]), 'pricing')
        ->create();

    Notification::fake();

    $response = $this->post(route('offers.reserve.store', $offer), [
        'variant_id' => $variant->id,
        'customer' => 'Jane Doe',
        'phone' => '0555123456',
        'travellers_number' => 3,
        'wilaya' => 'Oran',
        'room_type' => 'collectif',
        'include_feeding' => true,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('reservations', [
        'variant_id' => $variant->id,
        'customer' => 'Jane Doe',
        'phone' => '0555123456',
        'travellers_number' => 3,
        'wilaya' => 'Oran',
        'room_type' => 'collectif',
        'status' => 'pending',
        'include_feeding' => true,
        'total_price' => 36000, // (10000 + 2000) * 3
    ]);

    Notification::assertSentOnDemand(ReservationCreated::class);
});

test('public reservation defaults status to pending', function () {
    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()
        ->for($offer)
        ->has(OfferPricing::factory(), 'pricing')
        ->create();

    $this->post(route('offers.reserve.store', $offer), [
        'variant_id' => $variant->id,
        'customer' => 'Test User',
        'phone' => '0555000000',
        'travellers_number' => 1,
        'wilaya' => 'Alger',
        'room_type' => 'collectif',
    ]);

    $this->assertDatabaseHas('reservations', [
        'variant_id' => $variant->id,
        'customer' => 'Test User',
        'status' => 'pending',
    ]);
});

test('public reservation rejects invalid variant_id', function () {
    $offer = Offer::factory()->create();
    $otherOffer = Offer::factory()->create();
    $otherVariant = OfferVariant::factory()
        ->for($otherOffer)
        ->has(OfferPricing::factory(), 'pricing')
        ->create();

    $response = $this->post(route('offers.reserve.store', $offer), [
        'variant_id' => $otherVariant->id,
        'customer' => 'Test User',
        'phone' => '0555000000',
        'travellers_number' => 1,
        'wilaya' => 'Alger',
        'room_type' => 'collectif',
    ]);

    $response->assertSessionHasErrors('variant_id');
});

test('success page shows reservation details', function () {
    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()
        ->for($offer)
        ->has(OfferPricing::factory(), 'pricing')
        ->create();
    $reservation = Reservation::factory()
        ->for($variant, 'variant')
        ->create(['status' => 'pending']);

    $response = $this->get(
        route('offers.reserve.success', $offer).'?reservation='.$reservation->id
    );

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('offers/ReservationSuccess')
        ->has('offer')
        ->has('reservation')
        ->has('variant')
    );
});

test('success page rejects reservation from different offer', function () {
    $offer = Offer::factory()->create();
    $otherOffer = Offer::factory()->create();
    $otherVariant = OfferVariant::factory()->for($otherOffer)->create();
    $reservation = Reservation::factory()->for($otherVariant, 'variant')->create();

    $response = $this->get(
        route('offers.reserve.success', $offer).'?reservation='.$reservation->id
    );

    $response->assertNotFound();
});
