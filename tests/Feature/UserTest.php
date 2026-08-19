<?php

use App\Enums\Role;
use App\Models\User;

test('guests are redirected to login for user management', function () {
    $response = $this->get(route('users.index'));
    $response->assertRedirect(route('login'));
});

test('admins cannot access user management', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->get(route('users.index'));
    $response->assertForbidden();
});

test('superadmins can view users index', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user);

    $response = $this->get(route('users.index'));
    $response->assertOk();
});

test('superadmins can view create user form', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user);

    $response = $this->get(route('users.create'));
    $response->assertOk();
});

test('superadmins can create admin users', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user);

    $response = $this->post(route('users.store'), [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::Admin->value,
    ]);

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'role' => Role::Admin->value,
    ]);
});

test('superadmins can create other superadmin users', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user);

    $response = $this->post(route('users.store'), [
        'name' => 'New Super Admin',
        'email' => 'newsuper@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::SuperAdmin->value,
    ]);

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'New Super Admin',
        'email' => 'newsuper@example.com',
        'role' => Role::SuperAdmin->value,
    ]);
});

test('superadmins can view edit user form', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $targetUser = User::factory()->admin()->create();
    $this->actingAs($superAdmin);

    $response = $this->get(route('users.edit', $targetUser->id));
    $response->assertOk();
});

test('superadmins can update users', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $targetUser = User::factory()->admin()->create();
    $this->actingAs($superAdmin);

    $response = $this->put(route('users.update', $targetUser->id), [
        'name' => 'Updated Name',
        'email' => $targetUser->email,
        'role' => Role::SuperAdmin->value,
    ]);

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'name' => 'Updated Name',
        'role' => Role::SuperAdmin->value,
    ]);
});

test('superadmins can update user password', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $targetUser = User::factory()->admin()->create();
    $this->actingAs($superAdmin);

    $response = $this->put(route('users.update', $targetUser->id), [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'role' => Role::Admin->value,
    ]);

    $response->assertRedirect(route('users.index'));

    $targetUser->refresh();
    $this->assertTrue(Hash::check('newpassword123', $targetUser->password));
});

test('superadmins can delete users', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $targetUser = User::factory()->admin()->create();
    $this->actingAs($superAdmin);

    $response = $this->delete(route('users.destroy', $targetUser->id));
    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
});

test('superadmins cannot delete themselves', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $this->actingAs($superAdmin);

    $response = $this->delete(route('users.destroy', $superAdmin->id));
    $response->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
});

test('user creation validates required fields', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('users.store'), []);
    $response->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
});

test('user creation validates unique email', function () {
    $user = User::factory()->superAdmin()->create();
    $existingUser = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('users.store'), [
        'name' => 'Test User',
        'email' => $existingUser->email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::Admin->value,
    ]);

    $response->assertJsonValidationErrors(['email']);
});

test('user creation validates password confirmation', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user);

    $response = $this->postJson(route('users.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'differentpassword',
        'role' => Role::Admin->value,
    ]);

    $response->assertJsonValidationErrors(['password']);
});

test('user update validates required fields', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $targetUser = User::factory()->admin()->create();
    $this->actingAs($superAdmin);

    $response = $this->putJson(route('users.update', $targetUser->id), []);
    $response->assertJsonValidationErrors(['name', 'email', 'role']);
});

test('user update validates unique email excluding current user', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $targetUser = User::factory()->admin()->create();
    $this->actingAs($superAdmin);

    $response = $this->putJson(route('users.update', $targetUser->id), [
        'name' => $targetUser->name,
        'email' => $superAdmin->email,
        'role' => Role::Admin->value,
    ]);

    $response->assertJsonValidationErrors(['email']);
});

test('user update allows same email for current user', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $targetUser = User::factory()->admin()->create();
    $this->actingAs($superAdmin);

    $response = $this->putJson(route('users.update', $targetUser->id), [
        'name' => 'Updated Name',
        'email' => $targetUser->email,
        'role' => Role::Admin->value,
    ]);

    $response->assertRedirect();
});
