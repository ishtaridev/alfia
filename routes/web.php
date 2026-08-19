<?php

use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OfferImageController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicReservationController;
use App\Models\Offer;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $offers = Offer::with([
        'images',
        'variants' => function ($query): void {
            $query->future()->with('pricing')->orderBy('travel_date');
        },
    ])
        ->latest()
        ->take(6)
        ->get();

    return Inertia::render('Welcome', [
        'offers' => $offers,
    ]);
})->name('home');

// Public offer reservation routes (no auth required)
Route::get('offers/{offer}/reserve', [PublicReservationController::class, 'show'])
    ->name('offers.reserve');
Route::post('offers/{offer}/reserve', [PublicReservationController::class, 'store'])
    ->name('offers.reserve.store');
Route::get('offers/{offer}/reserve/success', [PublicReservationController::class, 'success'])
    ->name('offers.reserve.success');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('users', UserController::class)->except(['show']);

    Route::resource('offers', OfferController::class);
    Route::delete('offers/{offer}/images/{image}', [OfferImageController::class, 'destroy'])
        ->name('offers.images.destroy');

    Route::prefix('offer-variants/{offerVariant}')->name('offer-variants.')->group(function () {
        Route::resource('reservations', ReservationController::class);
    });
});

require __DIR__.'/settings.php';
