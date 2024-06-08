<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FlightController extends Controller
{
    /**
     * Show the index page with all the flights.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Flights/Index', [
            'flights' => $this->flights(),
            'user' => Auth::user(),
        ]);
    }

    /**
     * Get all available flights for the user.
     *
     * @return Collection|array
     */
    public function flights(): Collection|array
    {
        return Flight::availableFlights((Auth::user())->id)->get();
    }

    /**
     * Show a flight page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show(int $id): Response
    {
        return Inertia::render('Flights/Show', [
            'flight' => Flight::findWithSegmentsAndBooking($id),
            'user' => Auth::user(),
        ]);
    }
}
