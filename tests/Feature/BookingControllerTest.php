<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Segment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class BookingControllerTest
 *
 * This class tests the BookingController to ensure
 * the booking system works properly for users.
 */
class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This test creates a flight booking for test user.
     */
    public function test_user_can_book_a_flight()
    {
        $user = User::factory()->create();
        $flight = Flight::factory()->create();

        $response = $this->actingAs($user)->post('/bookings/store/'.$flight->id);

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'flight_id' => $flight->id,
        ]);
    }

    /**
     * This test checks if booking the same flight by
     * the same user is impossible.
     */
    public function test_user_cannot_book_same_flight_twice()
    {
        $user = User::factory()->create();
        $flight = Flight::factory()->create();

        Booking::create([
            'user_id' => $user->id,
            'flight_id' => $flight->id,
        ]);

        $response = $this->actingAs($user)->post('/bookings/store/'.$flight->id);

        $response->assertStatus(200);
        $response->assertJson(['error' => 'You have already booked this flight.']);
    }

    /**
     * This test creates and cancels a booking for a user.
     */
    public function test_user_can_cancel_a_booking()
    {
        $user = User::factory()->create();
        $flight = Flight::factory()->create();
        $booking = Booking::create([
            'user_id' => $user->id,
            'flight_id' => $flight->id,
        ]);

        $response = $this->actingAs($user)->post('/bookings/cancel/' . $booking->id);

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'cancelled' => true]);
    }

    /**
     * This test is making sure bookings are grouped by future, past and current flights.
     */
    public function test_user_bookings_are_grouped_correctly()
    {
        $user = User::factory()->create();

        $pastFlight = Flight::factory()->create();
        $currentFlight = Flight::factory()->create();
        $futureFlight = Flight::factory()->create();

        Segment::factory()->create([
            'flight_id' => $pastFlight->id,
            'departure_time' => Carbon::now()->subDays(2),
            'arrival_time' => Carbon::now()->subDay(),
        ]);

        Segment::factory()->create([
            'flight_id' => $currentFlight->id,
            'departure_time' => Carbon::now()->addHour(),
            'arrival_time' => Carbon::now()->addHours(2),
        ]);

        Segment::factory()->create([
            'flight_id' => $futureFlight->id,
            'departure_time' => Carbon::now()->addDays(2),
            'arrival_time' => Carbon::now()->addDays(2)->addHours(2),
        ]);

        Booking::create([
            'user_id' => $user->id,
            'flight_id' => $pastFlight->id,
        ]);

        Booking::create([
            'user_id' => $user->id,
            'flight_id' => $currentFlight->id,
        ]);

        Booking::create([
            'user_id' => $user->id,
            'flight_id' => $futureFlight->id,
        ]);

        $response = $this->actingAs($user)->get('/bookings?response=collection');

        $response->assertStatus(200);
        $bookings = $response->json();

        $this->assertArrayHasKey('past', $bookings);
        $this->assertArrayHasKey('current', $bookings);
        $this->assertArrayHasKey('future', $bookings);

        $this->assertCount(1, $bookings['past']);
        $this->assertCount(1, $bookings['current']);
        $this->assertCount(1, $bookings['future']);
    }

    public function test_user_cannot_book_more_tickets_than_available()
    {
        $user = User::factory()->create();
        $flight = Flight::factory()->create(['number_of_seats' => 3]);

        for ($i = 0; $i < 3; $i++) {
            $otherUser = User::factory()->create();
            Booking::factory()->create([
                'user_id' => $otherUser->id,
                'flight_id' => $flight->id,
                'cancelled' => false,
            ]);
        }

        $response = $this->actingAs($user)->post('/bookings/store/'.$flight->id);

        $response->assertStatus(200);
        $response->assertJson(['error' => 'No available seats left for this flight.']);

        $this->assertDatabaseCount('bookings', 3);
    }
}
