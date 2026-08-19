<?php

use App\Models\Offer;
use App\Models\Reservation;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard contains analytics data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $offer = Offer::factory()->create();
    $variant = $offer->variants()->create([
        'travel_date' => now()->addMonth(),
        'airport' => 'Algiers',
    ]);
    $reservation = Reservation::factory()->create([
        'variant_id' => $variant->id,
    ]);

    $currentMonth = now()->format('Y-m');
    $expectedMonths = collect(range(11, 0))
        ->mapWithKeys(fn ($monthsAgo) => [
            now()->subMonths($monthsAgo)->format('Y-m') => now()->subMonths($monthsAgo)->format('Y-m') === $currentMonth ? 1 : 0,
        ])
        ->all();

    $response = $this->get(route('dashboard'));
    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->has('stats')
            ->where('stats.total_offers', 1)
            ->where('stats.total_reservations', 1)
            ->has('statusBreakdown')
            ->has('monthlyReservations', 12)
            ->where('monthlyReservations', $expectedMonths)
            ->has('recentReservations')
            ->has('topOffers')
    );
});
