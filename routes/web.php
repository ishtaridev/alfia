<?php

use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OfferImageController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('offers', OfferController::class);
    Route::delete('offers/{offer}/images/{image}', [OfferImageController::class, 'destroy'])
        ->name('offers.images.destroy');
});

require __DIR__.'/settings.php';
