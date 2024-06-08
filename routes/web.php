<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('flights.index');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');
    Route::get('/flights/get', [FlightController::class, 'flights'])->name('flights.get');
    Route::get('/flights/{id}', [FlightController::class, 'show'])
        ->name('flights.show')->middleware('validate.flight');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/store/{flight_id}', [BookingController::class, 'store'])
        ->name('bookings.store')->middleware('validate.flight:flight_id');
    Route::post('/bookings/cancel/{id}', [BookingController::class, 'cancel'])
        ->name('bookings.cancel')->middleware('validate.booking:flight_id');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
