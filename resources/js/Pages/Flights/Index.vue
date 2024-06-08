<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="container mx-auto my-8 p-4 bg-gray-800 text-white rounded-lg shadow-md">
            <h1 class="text-3xl font-bold mb-6">Flights</h1>
            <FlightTable
                v-if="localFlights.length > 0"
                :title="'Available Flights'"
                :flights="localFlights"
                :showActions="true"
                @book-flight="bookFlight"
            />
            <div v-else>
                <p class="text-center py-4">No flights available</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FlightTable from '@/Components/FlightTable.vue';
import axios from 'axios';

const props = defineProps({
    flights: Array
});

const localFlights = ref(props.flights);
const pollInterval = ref(null);

const pollFlights = () => {
    axios.get('/flights/get')
        .then(response => {
            localFlights.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching flights:', error);
        });
};

const bookFlight = (flightId) => {
    axios.post('/bookings/store/' + flightId)
        .then(() => {
            alert('Booking confirmed.');
            pollFlights();
        })
        .catch((error) => {
            if (error.response && error.response.data.error) {
                alert('Error: ' + error.response.data.error);
            } else {
                alert('An unknown error occurred.');
            }
        });
};

onMounted(() => {
    pollFlights();
    pollInterval.value = setInterval(pollFlights, 10000);
});

onBeforeUnmount(() => {
    clearInterval(pollInterval.value);
});
</script>
