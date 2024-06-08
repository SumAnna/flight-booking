<?php

namespace Database\Factories;

use App\Models\Flight;
use App\Models\Segment;
use Illuminate\Database\Eloquent\Factories\Factory;

class SegmentFactory extends Factory
{
    protected $model = Segment::class;

    public function definition()
    {
        return [
            'flight_id' => Flight::factory(),
            'carrier_code' => $this->faker->randomElement(['AA', 'DL', 'UA']),
            'flight_number' => $this->faker->numberBetween(1000, 9999),
            'departure_iata' => $this->faker->randomElement(['LAX', 'SFO', 'JFK']),
            'arrival_iata' => $this->faker->randomElement(['LHR', 'CDG', 'FRA']),
            'departure_time' => $this->faker->dateTimeBetween('now', '+1 year'),
            'arrival_time' => $this->faker->dateTimeBetween('now', '+1 year'),
            'number_of_stops' => $this->faker->numberBetween(0, 1),
            'duration' => $this->faker->randomElement(['PT1H', 'PT2H', 'PT3H']),
        ];
    }
}
