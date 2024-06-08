<?php

namespace Database\Factories;

use App\Models\Flight;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlightFactory extends Factory
{
    protected $model = Flight::class;

    public function definition()
    {
        return [
            'ext_id' => $this->faker->unique()->uuid,
            'one_way' => $this->faker->boolean,
            'currency' => 'USD',
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'number_of_seats' => $this->faker->numberBetween(1, 100),
            'last_ticketing_date' => $this->faker->date(),
            'last_ticketing_date_time' => $this->faker->date() . ' ' . $this->faker->time(),
            'duration' => 'PT' . $this->faker->numberBetween(1, 10) . 'H',
        ];
    }
}
