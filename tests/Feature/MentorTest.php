<?php

use App\Models\Mentor;
use App\Models\User;

test('anyone can view the public mentor registration page', function () {
    $response = $this->get(route('mentors.register'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('mentors/Register')
        ->has('communes')
    );
});

test('guest can register as a mentor', function () {
    $response = $this->post(route('mentors.register.store'), [
        'full_name' => 'محمد أحمد',
        'phone' => '0555123456',
        'commune' => 'برج بوعريرج',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('mentors', [
        'full_name' => 'محمد أحمد',
        'phone' => '0555123456',
        'commune' => 'برج بوعريرج',
    ]);
});

test('registration rejects invalid phone format', function (string $phone) {
    $response = $this->post(route('mentors.register.store'), [
        'full_name' => 'محمد أحمد',
        'phone' => $phone,
        'commune' => 'برج بوعريرج',
    ]);

    $response->assertSessionHasErrors('phone');
})->with([
    'too short' => '055512345',
    'too long' => '05551234567',
    'invalid prefix' => '0455123456',
    'contains letters' => '05551234ab',
]);

test('registration rejects duplicate phone number', function () {
    Mentor::factory()->create(['phone' => '0555123456']);

    $response = $this->post(route('mentors.register.store'), [
        'full_name' => 'أحمد علي',
        'phone' => '0555123456',
        'commune' => 'برج بوعريرج',
    ]);

    $response->assertSessionHasErrors('phone');
});

test('registration rejects invalid commune', function () {
    $response = $this->post(route('mentors.register.store'), [
        'full_name' => 'محمد أحمد',
        'phone' => '0555123456',
        'commune' => 'Invalid Commune',
    ]);

    $response->assertSessionHasErrors('commune');
});

test('success page shows mentor details', function () {
    $mentor = Mentor::factory()->create();

    $response = $this->get(
        route('mentors.register.success').'?mentor='.$mentor->id
    );

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('mentors/Success')
        ->has('mentor')
    );
});

test('success page requires mentor query parameter', function () {
    $response = $this->get(route('mentors.register.success'));

    $response->assertNotFound();
});

test('admin can view mentors index', function () {
    $user = User::factory()->admin()->create();

    $response = $this->actingAs($user)->get(route('admin.mentors.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/mentors/Index')
        ->has('mentors')
        ->has('communes')
        ->has('filters')
    );
});

test('admin can filter mentors by name', function () {
    $user = User::factory()->admin()->create();
    Mentor::factory()->create(['full_name' => 'محمد أحمد']);
    Mentor::factory()->create(['full_name' => 'أحمد علي']);

    $response = $this->actingAs($user)->get(
        route('admin.mentors.index', ['search' => 'محمد'])
    );

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/mentors/Index')
        ->has('mentors.data', 1)
        ->where('mentors.data.0.full_name', 'محمد أحمد')
    );
});

test('admin can filter mentors by commune', function () {
    $user = User::factory()->admin()->create();
    Mentor::factory()->create([
        'full_name' => 'محمد أحمد',
        'commune' => 'برج بوعريرج',
    ]);
    Mentor::factory()->create([
        'full_name' => 'أحمد علي',
        'commune' => 'رأس الوادي',
    ]);

    $response = $this->actingAs($user)->get(
        route('admin.mentors.index', ['commune' => 'رأس الوادي'])
    );

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/mentors/Index')
        ->has('mentors.data', 1)
        ->where('mentors.data.0.full_name', 'أحمد علي')
    );
});

test('admin can combine name and commune filters', function () {
    $user = User::factory()->admin()->create();
    Mentor::factory()->create([
        'full_name' => 'محمد أحمد',
        'commune' => 'برج بوعريرج',
    ]);
    Mentor::factory()->create([
        'full_name' => 'محمد علي',
        'commune' => 'رأس الوادي',
    ]);
    Mentor::factory()->create([
        'full_name' => 'أحمد علي',
        'commune' => 'رأس الوادي',
    ]);

    $response = $this->actingAs($user)->get(
        route('admin.mentors.index', ['search' => 'محمد', 'commune' => 'رأس الوادي'])
    );

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('admin/mentors/Index')
        ->has('mentors.data', 1)
        ->where('mentors.data.0.full_name', 'محمد علي')
    );
});

test('guests are redirected to login for mentors index', function () {
    $response = $this->get(route('admin.mentors.index'));

    $response->assertRedirect(route('login'));
});
