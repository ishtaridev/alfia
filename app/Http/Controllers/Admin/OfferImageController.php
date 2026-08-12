<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfferImageController extends Controller
{
    public function destroy(Request $request, int $offer, OfferImage $image): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($image->offer_id !== $offer) {
            abort(404);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back();
    }
}
