<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\OfferVariant;
use App\Models\Reservation;
use App\Notifications\ReservationCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function index(Request $request, OfferVariant $offerVariant): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $query = $offerVariant->reservations()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $reservations = $query->paginate(12)->withQueryString();

        $allReservations = $offerVariant->reservations;

        $stats = [
            'total_reservations' => $allReservations->count(),
            'total_travellers' => $allReservations->sum('travellers_number'),
            'total_revenue' => $allReservations->sum('total_price'),
            'confirmed_count' => $allReservations->where('status', 'confirmed')->count(),
            'pending_count' => $allReservations->where('status', 'pending')->count(),
            'cancelled_count' => $allReservations->where('status', 'cancelled')->count(),
        ];

        return Inertia::render('reservations/Index', [
            'offerVariant' => $offerVariant->load('offer', 'pricing'),
            'reservations' => $reservations,
            'stats' => $stats,
            'filters' => [
                'status' => $request->input('status'),
            ],
        ]);
    }

    public function create(Request $request, OfferVariant $offerVariant): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        return Inertia::render('reservations/Create', [
            'offerVariant' => $offerVariant->load('offer', 'pricing'),
        ]);
    }

    public function store(StoreReservationRequest $request, OfferVariant $offerVariant): RedirectResponse
    {
        $data = $request->validated();
        $data['variant_id'] = $offerVariant->id;
        $data['total_price'] = $this->calculatePrice($offerVariant, $data);

        $reservation = Reservation::create($data);

        Notification::route('telegram', config('services.telegram-bot-api.chatId'))
            ->notify(new ReservationCreated($reservation));

        return to_route('offer-variants.reservations.index', $offerVariant);
    }

    public function show(Request $request, OfferVariant $offerVariant, Reservation $reservation): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($reservation->variant_id !== $offerVariant->id) {
            abort(404);
        }

        return Inertia::render('reservations/Show', [
            'offerVariant' => $offerVariant->load('offer', 'pricing'),
            'reservation' => $reservation,
        ]);
    }

    public function edit(Request $request, OfferVariant $offerVariant, Reservation $reservation): Response
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($reservation->variant_id !== $offerVariant->id) {
            abort(404);
        }

        return Inertia::render('reservations/Edit', [
            'offerVariant' => $offerVariant->load('offer', 'pricing'),
            'reservation' => $reservation,
        ]);
    }

    public function update(UpdateReservationRequest $request, OfferVariant $offerVariant, Reservation $reservation): RedirectResponse
    {
        if ($reservation->variant_id !== $offerVariant->id) {
            abort(404);
        }

        $data = $request->validated();
        $data['total_price'] = $this->calculatePrice($offerVariant, array_merge(
            $reservation->only(['room_type', 'travellers_number', 'include_feeding']),
            $data,
        ));

        $reservation->update($data);

        return to_route('offer-variants.reservations.index', $offerVariant);
    }

    public function destroy(Request $request, OfferVariant $offerVariant, Reservation $reservation): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($reservation->variant_id !== $offerVariant->id) {
            abort(404);
        }

        $reservation->delete();

        return back();
    }

    private function calculatePrice(OfferVariant $offerVariant, array $data): int
    {
        $offerVariant->load('pricing');

        $pricing = $offerVariant->pricing;
        if (! $pricing) {
            return 0;
        }

        $roomType = $data['room_type'] ?? RoomType::COLLECTIF->value;
        $travellers = $data['travellers_number'] ?? 1;
        $includeFeeding = $data['include_feeding'] ?? false;

        $roomPrice = match ($roomType) {
            RoomType::COLLECTIF->value => $pricing->collectif_room,
            RoomType::ROOM_OF_FOUR->value => $pricing->room_of_four,
            RoomType::ROOM_OF_THREE->value => $pricing->room_of_three,
            RoomType::ROOM_OF_TWO->value => $pricing->room_of_two,
            default => 0,
        };

        $total = $roomPrice * $travellers;

        if ($includeFeeding) {
            $total += $pricing->feeding * $travellers;
        }

        return $total;
    }
}
