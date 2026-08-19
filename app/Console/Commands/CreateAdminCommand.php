<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

#[Signature('admin:create {email} {password} {role=superadmin} {name?}')]
#[Description('Create a new admin or superadmin user')]
class CreateAdminCommand extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $roleValue = $this->argument('role');
        $name = $this->argument('name');

        $role = Role::tryFrom($roleValue);

        if ($role === null) {
            $this->error("Invalid role: {$roleValue}. Allowed values: ".implode(', ', array_column(Role::cases(), 'value')));

            return self::FAILURE;
        }

        if ($name === null) {
            $name = $role === Role::SuperAdmin ? 'Super Admin' : 'Admin';
        }

        try {
            Validator::make([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ], [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', Password::defaults()],
            ])->validate();
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $messages) {
                foreach ($messages as $message) {
                    $this->error($message);
                }
            }

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("A user with email [{$email}] already exists.");

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        $this->info("Created {$role->value} user [{$user->email}] with ID {$user->id}.");

        return self::SUCCESS;
    }
}
