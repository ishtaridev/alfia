<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $totalOffers = Offer::count();
        $totalReservations = Reservation::count();
        $totalRevenue = Reservation::sum('total_price') ?? 0;
        $totalUsers = User::count();
        $totalTravellers = Reservation::sum('travellers_number') ?? 0;

        $statusBreakdown = Reservation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $rawMonthlyReservations = Reservation::select(
            DB::raw("strftime('%Y-%m', created_at) as month"),
            DB::raw('count(*) as count')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->month => $item->count]);

        $monthlyReservations = collect(range(11, 0))
            ->mapWithKeys(function ($monthsAgo) use ($rawMonthlyReservations) {
                $month = now()->subMonths($monthsAgo)->format('Y-m');

                return [$month => $rawMonthlyReservations->get($month, 0)];
            })
            ->toArray();

        $recentReservations = Reservation::with(['variant.offer'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($reservation) => [
                'id' => $reservation->id,
                'customer' => $reservation->customer,
                'status' => $reservation->status,
                'total_price' => $reservation->total_price,
                'travellers_number' => $reservation->travellers_number,
                'created_at' => $reservation->created_at->toISOString(),
                'offer_title' => $reservation->variant?->offer?->title ?? 'N/A',
            ]);

        $topOffers = Offer::withCount('variants')
            ->with(['variants' => function ($query) {
                $query->withCount('reservations');
            }])
            ->get()
            ->map(fn ($offer) => [
                'title' => $offer->title,
                'reservations_count' => $offer->variants->sum('reservations_count'),
            ])
            ->sortByDesc('reservations_count')
            ->take(5)
            ->values()
            ->toArray();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_offers' => $totalOffers,
                'total_reservations' => $totalReservations,
                'total_revenue' => $totalRevenue,
                'total_users' => $totalUsers,
                'total_travellers' => $totalTravellers,
            ],
            'statusBreakdown' => $statusBreakdown,
            'monthlyReservations' => $monthlyReservations,
            'recentReservations' => $recentReservations,
            'topOffers' => $topOffers,
        ]);
    }
}
