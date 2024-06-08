<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'flight_id',
    ];

    /**
     * Get the flight that booking is for.
     *
     * @return BelongsTo
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * Get the user that booking is for.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user bookings grouped by status.
     *
     * @param int $userId
     *
     * @return Collection
     */
    public static function getUserBookings(int $userId): Collection
    {
        $earliestDepartureTimes = DB::table('segments')
            ->select('flight_id',
                DB::raw('MIN(departure_time) as earliest_departure_time'))
            ->groupBy('flight_id');

        return Booking::where('user_id', $userId)
            ->with(['flight' => function ($query) use ($earliestDepartureTimes) {
                $query->with(['segments' => function ($subquery) use ($earliestDepartureTimes) {
                    $subquery->joinSub($earliestDepartureTimes, 'earliest_times', function ($join) {
                        $join->on('segments.flight_id', '=', 'earliest_times.flight_id');
                    })->orderBy('earliest_times.earliest_departure_time');
                }]);
            }])
            ->get()
            ->map(function ($booking) {
                $flight = $booking->flight;
                $flight->booking_id = $booking->id;
                $flight->cancelled = $booking->cancelled;

                return $flight;
            })
            ->sortBy(function ($flight) {
                return $flight->segments->first()->earliest_departure_time;
            })
            ->groupBy('status');
    }
}
