<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Segment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'flight_id',
        'carrier_code',
        'flight_number',
        'departure_iata',
        'arrival_iata',
        'departure_time',
        'arrival_time',
        'number_of_stops',
        'duration',
    ];

    /**
     * Get the flight that owns the segment.
     *
     * @return BelongsTo
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }
}
