<?php

namespace App\Jobs;

use App\Models\Flight;
use App\Models\Segment;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\DB;

/**
 * Class ImportFlightData
 *
 * Job to import flight data into the database.
 */
class ImportFlightData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The flight data to be imported.
     *
     * @var array
     */
    protected array $flightData;

    /**
     * Create a new job instance.
     *
     * @param array $flightData The flight data to be imported.
     */
    public function __construct(array $flightData)
    {
        $this->flightData = $flightData['data'] ?? [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $batchSize = 50;

        LazyCollection::make(function () {
            foreach ($this->flightData as $flight) {
                yield $flight;
            }
        })
            ->chunk($batchSize)
            ->each(function ($chunk) {
                $flights = [];

                foreach ($chunk as $flight) {
                    if ($this->validateFlight($flight)) {
                        $oneWay = $flight['oneWay'] ?? false;

                        $flightEntry = [
                            'ext_id' => $flight['id'],
                            'one_way' => $oneWay,
                            'currency' => $flight['price']['currency'],
                            'price' => $flight['price']['total'],
                            'number_of_seats' => $flight['numberOfBookableSeats'],
                            'last_ticketing_date' => $flight['lastTicketingDate'],
                            'last_ticketing_date_time' => $flight['lastTicketingDateTime'],
                            'duration' => $flight['itineraries'][0]['duration'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $flights[] = $flightEntry;
                    } else {
                        Log::error('Missing or invalid data in flight', [
                            'flight' => $flight
                        ]);
                    }
                }

                if (!empty($flights)) {
                    try {
                        DB::transaction(function () use ($flights, $chunk) {
                            $segments = [];

                            foreach (array_chunk($flights, 50) as $flightChunk) {
                                Flight::upsert($flightChunk, ['ext_id'], [
                                    'one_way', 'currency', 'price', 'number_of_seats', 'last_ticketing_date', 'last_ticketing_date_time', 'duration', 'updated_at'
                                ]);
                            }

                            foreach ($chunk as $flight) {
                                $flightId = Flight::where('ext_id', $flight['id'])->value('id');

                                foreach ($flight['itineraries'] as $itinerary) {
                                    foreach ($itinerary['segments'] as $segment) {
                                        if ($this->validateSegment($segment)) {
                                            $segmentEntry = [
                                                'flight_id' => $flightId,
                                                'carrier_code' => $segment['carrierCode'],
                                                'flight_number' => $segment['number'],
                                                'departure_iata' => $segment['departure']['iataCode'],
                                                'arrival_iata' => $segment['arrival']['iataCode'],
                                                'departure_time' => $segment['departure']['at'],
                                                'arrival_time' => $segment['arrival']['at'],
                                                'number_of_stops' => $segment['numberOfStops'],
                                                'duration' => $segment['duration'],
                                                'created_at' => now(),
                                                'updated_at' => now(),
                                            ];

                                            $segments[] = $segmentEntry;
                                        } else {
                                            Log::error('Missing required data in segment', [
                                                'segment' => $segment
                                            ]);
                                        }
                                    }
                                }
                            }

                            if (!empty($segments)) {
                                foreach (array_chunk($segments, 50) as $segmentChunk) {
                                    Segment::upsert($segmentChunk, [
                                        'flight_id', 'carrier_code', 'flight_number', 'departure_time', 'arrival_time'
                                    ], [
                                        'departure_time', 'arrival_time', 'number_of_stops', 'duration', 'updated_at'
                                    ]);
                                }
                            }
                        });
                    } catch (Exception $e) {
                        Log::error(sprintf("Error occurred when inserting flights and segments into DB: %s", $e->getMessage()));
                        return;
                    }
                }
            });

        Log::info('Flight data import completed.');
    }

    /**
     * Validate the required fields in the flight data.
     *
     * @param array $flight The flight data to check.
     * @return bool True if all required fields are present and valid, false otherwise.
     */
    private function validateFlight(array $flight): bool
    {
        $requiredFields = [
            'id',
            'itineraries',
            'price',
            'numberOfBookableSeats',
            'lastTicketingDate',
            'lastTicketingDateTime'
        ];

        return $this->validateFields($flight, $requiredFields);
    }

    /**
     * Validate the required fields in the segment.
     *
     * @param array $segment The segment data to check.
     * @return bool True if all required fields are present and valid, false otherwise.
     */
    private function validateSegment(array $segment): bool
    {
        $requiredFields = [
            'carrierCode',
            'number',
            'departure.iataCode',
            'departure.at',
            'arrival.iataCode',
            'arrival.at',
            'numberOfStops',
            'duration',
        ];

        return $this->validateFields($segment, $requiredFields);
    }

    /**
     * Validate that the required fields are present in the array.
     *
     * @param array $array The array to check.
     * @param array $requiredFields The required fields.
     * @return bool True if all required fields are present, false otherwise.
     */
    private function validateFields(array $array, array $requiredFields): bool
    {
        foreach ($requiredFields as $field) {
            $keys = explode('.', $field);
            if ($this->getNestedValue($array, $keys) === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Safely get a nested value from an array.
     *
     * @param array $array The array to get the value from.
     * @param array $keys The keys to access the value.
     * @return mixed The value if found, null otherwise.
     */
    private function getNestedValue(array $array, array $keys)
    {
        foreach ($keys as $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return null;
            }
        }
        return $array;
    }
}
