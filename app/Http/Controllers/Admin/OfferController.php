<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use App\Models\OfferImage;
use App\Models\OfferVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class OfferController extends Controller
{
    public function index(Request $request): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $offers = Offer::with(['images', 'variants'])
            ->latest()
            ->paginate(12);

        return Inertia::render('offers/Index', [
            'offers' => $offers,
        ]);
    }

    public function create(Request $request): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        return Inertia::render('offers/Create');
    }

    public function store(StoreOfferRequest $request): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        DB::transaction(function () use ($request) {
            $offer = Offer::create($request->validated());

            foreach ($request->validated('variants') as $variantData) {
                $variant = $offer->variants()->create([
                    'travel_date' => $variantData['travel_date'],
                    'airport' => $variantData['airport'],
                ]);

                $variant->pricing()->create($variantData['pricing']);
            }

            if ($request->hasFile('images')) {
                $this->handleImageUploads($offer, $request->file('images'));
            }
        });

        return to_route('offers.index');
    }

    public function show(Request $request, Offer $offer): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $offer->load(['variants.pricing', 'images']);

        return Inertia::render('offers/Show', [
            'offer' => $offer,
        ]);
    }

    public function edit(Request $request, Offer $offer): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $offer->load(['variants.pricing', 'images']);

        return Inertia::render('offers/Edit', [
            'offer' => $offer,
        ]);
    }

    public function update(UpdateOfferRequest $request, Offer $offer): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        DB::transaction(function () use ($request, $offer) {
            $offer->update($request->only(['title', 'description']));

            if ($request->has('variants')) {
                $existingVariantIds = $offer->variants()->pluck('id')->toArray();
                $submittedVariantIds = collect($request->validated('variants'))
                    ->pluck('id')
                    ->filter()
                    ->toArray();

                $variantsToDelete = array_diff($existingVariantIds, $submittedVariantIds);
                if (! empty($variantsToDelete)) {
                    OfferVariant::whereIn('id', $variantsToDelete)
                        ->each(function ($variant) {
                            $variant->pricing()->delete();
                            $variant->delete();
                        });
                }

                foreach ($request->validated('variants') as $variantData) {
                    if (isset($variantData['id'])) {
                        $variant = $offer->variants()->findOrFail($variantData['id']);
                        $variant->update([
                            'travel_date' => $variantData['travel_date'],
                            'airport' => $variantData['airport'],
                        ]);
                        $variant->pricing()->updateOrCreate([], $variantData['pricing']);
                    } else {
                        $variant = $offer->variants()->create([
                            'travel_date' => $variantData['travel_date'],
                            'airport' => $variantData['airport'],
                        ]);
                        $variant->pricing()->create($variantData['pricing']);
                    }
                }
            }

            if ($request->has('deleted_image_ids') && is_array($request->deleted_image_ids)) {
                OfferImage::whereIn('id', $request->deleted_image_ids)
                    ->where('offer_id', $offer->id)
                    ->each(function (OfferImage $image) {
                        Storage::disk('public')->delete($image->path);
                        $image->delete();
                    });
            }

            if ($request->hasFile('images')) {
                $this->handleImageUploads($offer, $request->file('images'));
            }
        });

        return to_route('offers.show', $offer);
    }

    public function destroy(Request $request, Offer $offer): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        DB::transaction(function () use ($offer) {
            $offer->images()->each(function (OfferImage $image) {
                Storage::disk('public')->delete($image->path);
            });

            $offer->delete();
        });

        return to_route('offers.index');
    }

    private function handleImageUploads(Offer $offer, array $files): void
    {
        $maxOrder = $offer->images()->max('order') ?? 0;

        foreach ($files as $index => $file) {
            $path = Image::fromUpload($file)
                ->toWebp()
                ->store('offers/'.$offer->id, 'public');
            $offer->images()->create([
                'path' => $path,
                'order' => $maxOrder + $index + 1,
            ]);
        }
    }
}
