<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $users = User::latest()
            ->paginate(10);

        return Inertia::render('admin/users/Index', [
            'users' => $users,
        ]);
    }

    public function create(Request $request): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => $request->validated('role'),
        ]);

        return to_route('users.index');
    }

    public function edit(Request $request, User $user): Response
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        return Inertia::render('admin/users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'role' => $request->validated('role'),
        ];

        if ($request->has('password') && $request->validated('password') !== null) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        return to_route('users.index');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($user->id === $request->user()->id) {
            abort(403, 'You cannot delete yourself.');
        }

        $user->delete();

        return to_route('users.index');
    }
}
