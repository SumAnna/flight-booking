<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    /**
     * Save a booking into the DB.
     *
     * @param int $flightId
     *
     * @return JsonResponse
     */
    public function store(int $flightId): JsonResponse
    {
        $flight = Flight::withCount(['bookings' => function ($query) {
            $query->where('cancelled', false);
        }])->find($flightId);

        $existingBooking = Booking::where('user_id', Auth::id())
            ->where('flight_id', $flightId)
            ->first();

        if ($existingBooking) {
            return response()->json(['error' => 'You have already booked this flight.']);
        }

        if ($flight->bookings_count >= $flight->number_of_seats) {
            return response()->json(['error' => 'No available seats left for this flight.']);
        }

        Booking::create([
            'user_id' => Auth::id(),
            'flight_id' => $flightId,
        ]);

        return response()->json(['success' => 'Booking confirmed.']);
    }

    /**
     * Cancel a booking in the DB.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function cancel(int $id): JsonResponse
    {
        $booking = Booking::find($id);

        if ($booking->user_id == Auth::id()) {
            $booking->cancelled = true;
            $booking->save();

            return response()->json(['success' => 'Booking cancelled.']);
        }

        return response()->json(['error' => 'Unauthorized access.']);
    }

    /**
     * Display the flights categorized as past, current, and future flights.
     *
     * @param Request $request
     *
     * @return Response|Collection
     */
    public function index(Request $request): Response|Collection
    {
        $user = Auth::user();

        $responseType = $request->query('response', 'inertia');

        $bookings = Booking::where('user_id', $user->id)
            ->with(['flight.segments' => function ($query) {
                $query->orderBy('departure_time');
            }])
            ->get()
            ->map(function ($booking) {
                $flight = $booking->flight;
                $flight->booking_id = $booking->id;
                $flight->cancelled = $booking->cancelled;
                return $flight;
            })
            ->groupBy('status');

        if ($responseType === 'inertia') {
            return Inertia::render('Bookings/Show', [
                'flights' => $bookings,
                'user' => $user,
            ]);
        }

        return $bookings;
    }
}
