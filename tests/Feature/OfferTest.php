<?php

use App\Models\Offer;
use App\Models\OfferPricing;
use App\Models\OfferVariant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests are redirected to login', function () {
    $response = $this->get(route('offers.index'));
    $response->assertRedirect(route('login'));
});

test('admins can view offers index', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->get(route('offers.index'));
    $response->assertOk();
});

test('superadmins can view offers index', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user);

    $response = $this->get(route('offers.index'));
    $response->assertOk();
});

test('admins can view create offer form', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->get(route('offers.create'));
    $response->assertOk();
});

test('admins can create an offer with variants and pricing', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $offerData = [
        'title' => 'Summer Getaway to Istanbul',
        'description' => '<p>An amazing trip</p>',
        'variants' => [
            [
                'travel_date' => now()->addWeek()->format('Y-m-d'),
                'airport' => 'Algiers',
                'pricing' => [
                    'collectif_room' => 50000,
                    'room_of_four' => 60000,
                    'room_of_three' => 70000,
                    'room_of_two' => 80000,
                    'feeding' => 10000,
                ],
            ],
        ],
    ];

    $response = $this->postJson(route('offers.store'), $offerData);
    $response->assertRedirect();

    $this->assertDatabaseHas('offers', ['title' => 'Summer Getaway to Istanbul']);
    $offer = Offer::where('title', 'Summer Getaway to Istanbul')->first();
    $this->assertStringStartsWith('ALFIA-', $offer->code);

    $variant = OfferVariant::where('offer_id', $offer->id)->first();
    $this->assertNotNull($variant);
    $this->assertEquals(now()->addWeek()->format('Y-m-d'), $variant->travel_date->format('Y-m-d'));
    $this->assertEquals('Algiers', $variant->airport);

    $this->assertDatabaseHas('offer_pricings', [
        'offer_variant_id' => $variant->id,
        'collectif_room' => 50000,
        'room_of_four' => 60000,
        'room_of_three' => 70000,
        'room_of_two' => 80000,
        'feeding' => 10000,
    ]);
});

test('admins can view an offer', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()->for($offer)->create();
    OfferPricing::factory()->for($variant, 'variant')->create();
    $offer->load(['variants.pricing', 'images']);

    $response = $this->get(route('offers.show', $offer->code));
    $response->assertOk();
});

test('admins can update an offer', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()->for($offer)->create();
    OfferPricing::factory()->for($variant, 'variant')->create();

    $updateData = [
        'title' => 'Updated Offer Title',
        'description' => '<p>Updated description</p>',
        'variants' => [
            [
                'id' => $variant->id,
                'travel_date' => now()->addMonth()->format('Y-m-d'),
                'airport' => 'Oran',
                'pricing' => [
                    'collectif_room' => 55000,
                    'room_of_four' => 65000,
                    'room_of_three' => 75000,
                    'room_of_two' => 85000,
                    'feeding' => 12000,
                ],
            ],
        ],
    ];

    $response = $this->putJson(route('offers.update', $offer->code), $updateData);
    $response->assertRedirect();

    $this->assertDatabaseHas('offers', [
        'id' => $offer->id,
        'title' => 'Updated Offer Title',
    ]);

    $this->assertDatabaseHas('offer_pricings', [
        'offer_variant_id' => $variant->id,
        'collectif_room' => 55000,
    ]);
});

test('admins can add new variants during update', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()->for($offer)->create();
    OfferPricing::factory()->for($variant, 'variant')->create();

    $updateData = [
        'title' => $offer->title,
        'variants' => [
            [
                'id' => $variant->id,
                'travel_date' => now()->addMonth()->format('Y-m-d'),
                'airport' => 'Oran',
                'pricing' => [
                    'collectif_room' => 55000,
                    'room_of_four' => 65000,
                    'room_of_three' => 75000,
                    'room_of_two' => 85000,
                    'feeding' => 12000,
                ],
            ],
            [
                'travel_date' => now()->addMonths(2)->format('Y-m-d'),
                'airport' => 'Constantine',
                'pricing' => [
                    'collectif_room' => 45000,
                    'room_of_four' => 55000,
                    'room_of_three' => 65000,
                    'room_of_two' => 75000,
                    'feeding' => 8000,
                ],
            ],
        ],
    ];

    $response = $this->putJson(route('offers.update', $offer->code), $updateData);

    $this->assertDatabaseHas('offer_variants', [
        'offer_id' => $offer->id,
        'airport' => 'Constantine',
    ]);

    $this->assertEquals(2, $offer->fresh()->variants()->count());
});

test('admins can delete an offer', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $offer = Offer::factory()->create();
    $variant = OfferVariant::factory()->for($offer)->create();
    OfferPricing::factory()->for($variant, 'variant')->create();

    $response = $this->delete(route('offers.destroy', $offer->code));
    $response->assertRedirect();

    $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
    $this->assertDatabaseMissing('offer_variants', ['id' => $variant->id]);
});

test('offer creation validates required fields', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('offers.store'), []);
    $response->assertJsonValidationErrors(['title', 'variants']);
});

test('offer creation validates variant fields', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('offers.store'), [
        'title' => 'Test Offer',
        'variants' => [
            [
                'travel_date' => '',
                'airport' => '',
                'pricing' => [
                    'collectif_room' => -1,
                ],
            ],
        ],
    ]);

    $response->assertJsonValidationErrors([
        'variants.0.travel_date',
        'variants.0.airport',
        'variants.0.pricing.collectif_room',
        'variants.0.pricing.room_of_four',
        'variants.0.pricing.room_of_three',
        'variants.0.pricing.room_of_two',
        'variants.0.pricing.feeding',
    ]);
});

test('future scope returns only future date variants', function () {
    $offer = Offer::factory()->create();

    $futureVariant = OfferVariant::factory()
        ->for($offer)
        ->create(['travel_date' => now()->addWeek()]);

    $pastVariant = OfferVariant::factory()
        ->for($offer)
        ->create(['travel_date' => now()->subWeek()]);

    $futureVariants = $offer->variants()->future()->get();

    $this->assertCount(1, $futureVariants);
    $this->assertTrue($futureVariants->first()->id === $futureVariant->id);
});

test('admin uploaded offer images are converted to webp', function () {
    if (! extension_loaded('gd') && ! extension_loaded('imagick')) {
        $this->markTestSkipped('No image extension available.');
    }

    Storage::fake('public');

    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $offerData = [
        'title' => 'Offer With Image',
        'description' => '<p>An offer</p>',
        'variants' => [
            [
                'travel_date' => now()->addWeek()->format('Y-m-d'),
                'airport' => 'Algiers',
                'pricing' => [
                    'collectif_room' => 50000,
                    'room_of_four' => 60000,
                    'room_of_three' => 70000,
                    'room_of_two' => 80000,
                    'feeding' => 10000,
                ],
            ],
        ],
        'images' => [
            UploadedFile::fake()->image('offer-photo.jpg', 800, 600),
        ],
    ];

    $response = $this->postJson(route('offers.store'), $offerData);
    $response->assertRedirect();

    $offer = Offer::where('title', 'Offer With Image')->first();
    $this->assertNotNull($offer);
    $this->assertCount(1, $offer->images);

    $image = $offer->images->first();
    Storage::disk('public')->assertExists($image->path);
    $this->assertStringEndsWith('.webp', $image->path);
});
