<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

afterEach(function () {
    Password::defaults(fn (): ?Password => null);
});

test('creates a superadmin with default name', function () {
    $this->artisan('admin:create', [
        'email' => 'superadmin@example.com',
        'password' => 'password123',
    ])->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'name' => 'Super Admin',
        'email' => 'superadmin@example.com',
        'role' => Role::SuperAdmin->value,
    ]);

    $this->assertNotNull(User::where('email', 'superadmin@example.com')->value('email_verified_at'));
});

test('creates an admin with explicit name', function () {
    $this->artisan('admin:create', [
        'email' => 'admin@example.com',
        'password' => 'password123',
        'role' => Role::Admin->value,
        'name' => 'Content Editor',
    ])->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'name' => 'Content Editor',
        'email' => 'admin@example.com',
        'role' => Role::Admin->value,
    ]);
});

test('rejects invalid email format', function () {
    $this->artisan('admin:create', [
        'email' => 'not-an-email',
        'password' => 'password123',
        'role' => Role::SuperAdmin->value,
    ])->assertFailed();
});

test('rejects duplicate email', function () {
    User::factory()->admin()->create(['email' => 'admin@example.com']);

    $this->artisan('admin:create', [
        'email' => 'admin@example.com',
        'password' => 'password123',
        'role' => Role::SuperAdmin->value,
    ])->assertFailed();
});

test('rejects invalid role', function () {
    $this->artisan('admin:create', [
        'email' => 'admin@example.com',
        'password' => 'password123',
        'role' => 'invalid-role',
    ])->assertFailed();
});

test('rejects passwords that do not meet configured defaults', function () {
    Password::defaults(
        fn (): Password => Password::min(12)->mixedCase()->letters()->numbers()->symbols()
    );

    $this->artisan('admin:create', [
        'email' => 'admin@example.com',
        'password' => 'short',
        'role' => Role::SuperAdmin->value,
    ])->assertFailed();
});
