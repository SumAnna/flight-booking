<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'ext_id',
        'one_way',
        'currency',
        'price',
        'number_of_seats',
        'last_ticketing_date',
        'last_ticketing_date_time',
        'duration',
    ];

    protected $appends = [
        'start_point',
        'connection_flights',
        'final_destination',
        'status',
    ];

    /**
     * Get all the bookings for the flight.
     *
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all the segments for the flight.
     *
     * @return HasMany
     */
    public function segments(): HasMany
    {
        return $this->hasMany(Segment::class);
    }

    /**
     * Find a flight with its segments and the count of non-cancelled bookings.
     *
     * @param int $id The flight ID.
     *
     * @return Flight|null The flight instance or null if not found.
     */
    public static function findWithSegmentsAndBooking(int $id): ?Flight
    {
        return self::with('segments')
            ->leftJoin('bookings', function ($join) {
                $join->on('flights.id', '=', 'bookings.flight_id')
                    ->where('bookings.cancelled', '=', false);
            })
            ->where('flights.id', $id)
            ->select('flights.*',
                DB::raw('COUNT(bookings.id) as bookings_count'))
            ->groupBy('flights.id')
            ->first();
    }

    /**
     * Get all the available flights with bookings, segments parts.
     *
     * @param Builder $query
     * @param int     $userId
     *
     * @return Builder
     */
    public function scopeAvailableFlights(Builder $query, int $userId): Builder
    {
        $now = Carbon::now();

        return $query->with('segments')
            ->where('flights.last_ticketing_date_time', '>', $now)
            ->where('flights.number_of_seats', '>', 0)
            ->leftJoin('bookings', function ($join) {
                $join->on('flights.id', '=', 'bookings.flight_id')
                    ->where('bookings.cancelled', '=', false);
            })
            ->leftJoin(DB::raw('(SELECT flight_id, MIN(departure_time) AS min_departure_time FROM segments GROUP BY flight_id) AS earliest_departures'), function($join) {
                $join->on('flights.id', '=', 'earliest_departures.flight_id');
            })
            ->select('flights.*', DB::raw('COUNT(bookings.id) as bookings_count'))
            ->orderBy('earliest_departures.min_departure_time')
            ->groupBy('flights.id')
            ->havingRaw('flights.number_of_seats > COUNT(bookings.id)');
    }

    /**
     * Get status of the flight.
     *
     * @return string
     */
    public function getStatusAttribute(): string
    {
        if ($this->cancelled == true) {
            return 'cancelled';
        }

        $now = Carbon::now();
        $tomorrow = $now->copy()->addDay();

        $hasPastSegment = false;
        $hasCurrentSegment = false;
        $hasFutureSegment = false;

        foreach ($this->segments as $segment) {
            $departureTime = Carbon::parse($segment->departure_time);
            if ($departureTime->lt($now)) {
                $hasPastSegment = true;
            } elseif ($departureTime->between($now, $tomorrow)) {
                $hasCurrentSegment = true;
            } else {
                $hasFutureSegment = true;
            }
        }

        if ($hasPastSegment && !$hasCurrentSegment && !$hasFutureSegment) {
            return 'past';
        } elseif ($hasCurrentSegment) {
            return 'current';
        }

        return 'future';
    }

    /**
     * Access the number of connection flights.
     *
     * @return int
     */
    public function getConnectionFlightsAttribute(): int
    {
        return $this->segments->count() - 1;
    }

    /**
     * Access the start point of the flight.
     *
     * @return string
     */
    public function getStartPointAttribute(): string
    {
        return $this->segments->first()->departure_iata ?? '';
    }

    /**
     * Get the final destination of the flight.
     *
     * @return string
     */
    public function getFinalDestinationAttribute(): string
    {
        return $this->segments->last()->arrival_iata ?? '';
    }
}
