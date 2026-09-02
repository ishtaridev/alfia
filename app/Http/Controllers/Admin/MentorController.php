<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MentorController extends Controller
{
    public function index(Request $request): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $query = Mentor::query()->latest();

        if ($request->filled('search')) {
            $query->where('full_name', 'like', '%'.$request->input('search').'%');
        }

        if ($request->filled('commune')) {
            $query->where('commune', $request->input('commune'));
        }

        $mentors = $query->paginate(15);

        return Inertia::render('admin/mentors/Index', [
            'mentors' => Inertia::scroll($mentors),
            'communes' => config('communes.bordj_bou_arreridj'),
            'filters' => [
                'search' => $request->input('search'),
                'commune' => $request->input('commune'),
            ],
        ]);
    }
}
