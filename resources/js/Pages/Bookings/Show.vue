<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="container mx-auto my-8 p-4 bg-gray-800 text-white rounded-lg shadow-md">
            <h1 class="text-3xl font-bold mb-6">My Bookings</h1>
            <div v-if="localFlights.past || localFlights.current || localFlights.future || localFlights.cancelled">
                <FlightTable
                    v-if="localFlights.past && localFlights.past.length > 0"
                    :title="'Past Flights'"
                    :flights="localFlights.past"
                />
                <FlightTable
                    v-if="localFlights.current && localFlights.current.length > 0"
                    :title="'Current Flights'"
                    :flights="localFlights.current"
                />
                <FlightTable
                    v-if="localFlights.future && localFlights.future.length > 0"
                    :title="'Future Flights'"
                    :flights="localFlights.future"
                    :showActions="true"
                    @delete-booking="deleteBooking"
                />
                <FlightTable
                    v-if="localFlights.cancelled && localFlights.cancelled.length > 0"
                    :title="'Cancelled Flights'"
                    :flights="localFlights.cancelled"
                />
            </div>
            <div v-else>
                <p class="text-center py-4">No flights available</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import FlightTable from '@/Components/FlightTable.vue';
import axios from 'axios';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    flights: Object,
});

const localFlights = ref(props.flights);

watch(() => props.flights, (newFlights) => {
    localFlights.value = newFlights;
});

const deleteBooking = (bookingId) => {
    if (confirm('Are you sure you want to cancel this booking?')) {
        axios.post(`/bookings/cancel/${bookingId}`)
            .then(response => {
                alert(response.data.success);
                pollBookings();
            })
            .catch(error => {
                if (error.response && error.response.data.error) {
                    alert(error.response.data.error);
                } else {
                    alert('An unknown error occurred.');
                }
            });
    }
};

const pollBookings = () => {
    axios.get('/bookings?response=collection')
        .then(response => {
            localFlights.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching bookings:', error);
        });
};
</script>
