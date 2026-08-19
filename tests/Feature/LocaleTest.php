<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows guests to update the locale', function () {
    $response = $this->postJson('/settings/locale', ['locale' => 'ar']);

    $response->assertOk()
        ->assertJson(['locale' => 'ar']);

    expect(session('locale'))->toBe('ar');
});

it('allows authenticated users to update the locale', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/settings/locale', ['locale' => 'fr']);

    $response->assertOk()
        ->assertJson(['locale' => 'fr']);

    expect(session('locale'))->toBe('fr');
});

it('rejects unsupported locales', function () {
    $response = $this->postJson('/settings/locale', ['locale' => 'de']);

    $response->assertUnprocessable();
});

it('persists the selected locale across requests', function () {
    $this->postJson('/settings/locale', ['locale' => 'ar'])->assertOk();

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->has('locale')
        ->where('locale', 'ar')
        ->where('direction', 'rtl')
    );
});
