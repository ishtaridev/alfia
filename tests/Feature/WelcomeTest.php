<?php

use App\Models\Offer;
use App\Models\OfferVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the welcome page', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->has('offers')
        );
});

it('displays agency branding in the page metadata', function () {
    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertSee('Alfia')
        ->assertInertia(fn ($page) => $page->component('Welcome'));
});

it('lists featured offers with future variants', function () {
    $offer = Offer::factory()->create(['title' => 'Umrah Spring Package']);
    OfferVariant::factory()->create([
        'offer_id' => $offer->id,
        'travel_date' => now()->addMonth(),
    ]);

    $response = $this->get('/');

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Welcome')
            ->has('offers', 1)
            ->where('offers.0.title', 'Umrah Spring Package')
        );
});
