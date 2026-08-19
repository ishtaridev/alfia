<?php

namespace App\Http\Controllers;

use App\Enums\RoomType;
use App\Http\Requests\StorePublicReservationRequest;
use App\Models\Offer;
use App\Models\OfferVariant;
use App\Models\Reservation;
use App\Notifications\ReservationCreated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class PublicReservationController extends Controller
{
    public function show(Offer $offer): Response
    {
        $offer->load([
            'images',
            'variants' => function ($query): void {
                $query->future()->with('pricing');
            },
        ]);

        return Inertia::render('offers/Reserve', [
            'offer' => $offer,
        ]);
    }

    public function store(StorePublicReservationRequest $request, Offer $offer): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = 'pending';
        $data['total_price'] = $this->calculatePrice($data);

        $reservation = Reservation::create($data);

        Notification::route('telegram', config('services.telegram-bot-api.chatId'))
            ->notify(new ReservationCreated($reservation));

        return redirect()->to(
            route('offers.reserve.success', $offer).'?reservation='.$reservation->id
        );
    }

    public function success(Offer $offer, Request $request): Response
    {
        $reservationId = $request->query('reservation');
        if (! $reservationId) {
            abort(404);
        }

        $reservation = Reservation::findOrFail($reservationId);
        $variant = OfferVariant::findOrFail($reservation->variant_id);

        if ($variant->offer_id !== $offer->id) {
            abort(404);
        }

        return Inertia::render('offers/ReservationSuccess', [
            'offer' => $offer->load('images'),
            'reservation' => $reservation,
            'variant' => $variant,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function calculatePrice(array $data): int
    {
        $variant = OfferVariant::with('pricing')->findOrFail($data['variant_id']);
        $pricing = $variant->pricing;

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
