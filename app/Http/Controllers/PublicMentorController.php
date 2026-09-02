<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMentorRequest;
use App\Models\Mentor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicMentorController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('mentors/Register', [
            'communes' => config('communes.bordj_bou_arreridj'),
        ]);
    }

    public function store(StoreMentorRequest $request): RedirectResponse
    {
        $mentor = Mentor::create($request->validated());

        return redirect()->to(
            route('mentors.register.success').'?mentor='.$mentor->id
        );
    }

    public function success(Request $request): Response
    {
        $mentorId = $request->query('mentor');
        if (! $mentorId) {
            abort(404);
        }

        $mentor = Mentor::findOrFail($mentorId);

        return Inertia::render('mentors/Success', [
            'mentor' => $mentor,
        ]);
    }
}
