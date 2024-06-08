<template>
    <div class="container mx-auto my-8 p-4 bg-gray-800 text-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold mb-6">Flight Details</h1>
        <div class="mb-4">
            <p><strong>Start Destination:</strong> {{ flight.start_point }}</p>
            <p><strong>Final Destination:</strong> {{ flight.final_destination }}</p>
            <p><strong>Duration:</strong> {{ flight.duration }}</p>
            <p><strong>Connection Flights:</strong> {{ flight.connection_flights && flight.connection_flights > 0 ? flight.connection_flights : 'None' }}</p>
            <p><strong>Seats:</strong> {{ flight.number_of_seats }}</p>
            <p><strong>Tickets Left:</strong> {{ seatsLeft }}</p>
            <p><strong>Price:</strong> {{ flight.price }} {{ flight.currency }}</p>
            <p><strong>Last Ticketing Date:</strong> {{ formatDate(flight.last_ticketing_date) }}</p>
            <p><strong>Last Ticketing Time:</strong> {{ formatDateTime(flight.last_ticketing_date_time) }}</p>
        </div>
        <h2 class="text-2xl font-bold mb-4">Segments</h2>
        <ul>
            <li v-for="segment in flight.segments" :key="segment.id" class="mb-2">
                <p><strong>Carrier:</strong> {{ segment.carrier_code }}</p>
                <p><strong>Flight Number:</strong> {{ segment.flight_number }}</p>
                <p><strong>Departure:</strong> {{ segment.departure_iata }} at {{ formatDateTime(segment.departure_time) }}</p>
                <p><strong>Arrival:</strong> {{ segment.arrival_iata }} at {{ formatDateTime(segment.arrival_time) }}</p>
                <p><strong>Duration:</strong> {{ segment.duration }}</p>
                <p><strong>Stops:</strong> {{ segment.number_of_stops }}</p>
            </li>
        </ul>
        <button @click="backToFlights" class="mt-4 bg-gray-600 hover:bg-gray-700 text-white py-1 px-3 rounded">Back to Flights</button>
    </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
    props: {
        flight: Object
    },
    computed: {
        seatsLeft() {
            return this.flight.number_of_seats - (this.flight.bookings_count || 0);
        }
    },
    methods: {
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        },
        formatDateTime(dateTimeString) {
            return new Date(dateTimeString).toLocaleString();
        },
        backToFlights() {
            Inertia.get('/flights');
        }
    }
}
</script>

<style scoped>
.container {
    max-width: 800px;
}
</style>
