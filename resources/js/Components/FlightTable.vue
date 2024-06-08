<template>
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">{{ title }}</h2>
        <table class="min-w-full bg-gray-900 rounded-lg">
            <thead>
            <tr>
                <th class="py-2 px-4 border-b border-gray-700">Start Destination</th>
                <th class="py-2 px-4 border-b border-gray-700">Final Destination</th>
                <th class="py-2 px-4 border-b border-gray-700">Duration</th>
                <th class="py-2 px-4 border-b border-gray-700">Segments</th>
                <th class="py-2 px-4 border-b border-gray-700">Stops</th>
                <th class="py-2 px-4 border-b border-gray-700">Seats</th>
                <th class="py-2 px-4 border-b border-gray-700">Price</th>
                <th class="py-2 px-4 border-b border-gray-700" v-if="showActions">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="flights.length === 0">
                <td colspan="8" class="text-center py-4">No {{ title.toLowerCase() }}</td>
            </tr>
            <template v-else>
                <tr v-for="flight in flights" :key="flight.id" class="hover:bg-zinc-700">
                    <td class="py-2 px-4 border-b border-gray-700">{{ flight.start_point }}</td>
                    <td class="py-2 px-4 border-b border-gray-700">{{ flight.final_destination }}</td>
                    <td class="py-2 px-4 border-b border-gray-700">{{ flight.duration }}</td>
                    <td class="py-2 px-4 border-b border-gray-700">
                        <ul>
                            <li v-for="segment in flight.segments" :key="segment.id">
                                <div class="segment-info">
                                    <span>{{ segment.departure_iata }} <span class="datetime">({{ formatDate(segment.departure_time) }})</span></span>
                                    <span> - </span>
                                    <span>{{ segment.arrival_iata }} <span class="datetime">({{ formatDate(segment.arrival_time) }})</span></span>
                                </div>
                                <div class="duration">Duration: {{ segment.duration }}</div>
                            </li>
                        </ul>
                    </td>
                    <td class="py-2 px-4 border-b border-gray-700">{{ flight.connection_flights ?? '-' }}</td>
                    <td class="py-2 px-4 border-b border-gray-700">{{ flight.number_of_seats }}</td>
                    <td class="py-2 px-4 border-b border-gray-700">{{ flight.price }} {{ flight.currency }}</td>
                    <td class="py-2 px-4 border-b border-gray-700" v-if="showActions">
                        <div v-if="title === 'Available Flights'" class="flex space-x-2">
                            <form @submit.prevent="emitBookFlight(flight.id)">
                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white py-1 px-3 rounded">Save Flight</button>
                            </form>
                            <Link
                                :href="route('flights.show', flight.id)"
                                class="bg-gray-600 hover:bg-gray-700 text-white py-1 px-3 rounded"
                            >
                                Details
                            </Link>
                        </div>
                        <div v-else>
                            <button @click="emitDeleteBooking(flight.booking_id)" class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded">Delete</button>
                        </div>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    title: String,
    flights: Array,
    showActions: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['book-flight', 'delete-booking']);

const formatDate = (dateString) => {
    const options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    };
    return new Date(dateString).toLocaleString('en-US', options);
};

const emitBookFlight = (flightId) => {
    emit('book-flight', flightId);
};

const emitDeleteBooking = (bookingId) => {
    emit('delete-booking', bookingId);
};
</script>

<style scoped>
.container {
    max-width: 800px;
}

.segment-info {
    display: flex;
    flex-direction: column;
}

.datetime {
    font-size: 0.9rem;
    color: #bbb;
}

.duration {
    font-size: 0.85rem;
    color: #888;
    margin-top: 0.2rem;
}
</style>
