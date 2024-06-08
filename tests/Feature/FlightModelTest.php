<?php

namespace Tests\Unit;

use App\Models\Flight;
use App\Models\Segment;
use Tests\TestCase;

/**
 * Class FlightModelTest
 *
 * This class tests the Flight Model to ensure
 * the model has all the required attributes.
 */
class FlightModelTest extends TestCase
{
    /**
     * This test is to ensure that start city/town is shown correctly.
     */
    public function test_start_point_attribute()
    {
        $flight = Flight::factory()->create();
        Segment::factory()->create([
            'flight_id' => $flight->id,
            'departure_iata' => 'LHR',
        ]);

        $this->assertEquals('LHR', $flight->start_point);
    }

    /**
     * This test is to ensure that final destination is handled properly.
     */
    public function test_final_destination_attribute()
    {
        $flight = Flight::factory()->create();
        Segment::factory()->create([
            'flight_id' => $flight->id,
            'arrival_iata' => 'CDG',
        ]);

        $this->assertEquals('CDG', $flight->final_destination);
    }

    /**
     * This test is to ensure that connection flights
     * number is calculated right.
     */
    public function test_connection_flights_attribute()
    {
        $flight = Flight::factory()->create();
        Segment::factory()->count(3)->create(['flight_id' => $flight->id]);

        $this->assertEquals(2, $flight->connection_flights);
    }
}
