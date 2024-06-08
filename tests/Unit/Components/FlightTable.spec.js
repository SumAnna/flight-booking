import { mount } from '@vue/test-utils';
import FlightTable from '@/Components/FlightTable.vue';

describe('FlightTable.vue', () => {
    it('renders props.flights when passed', () => {
        const flights = [
            {
                id: 1,
                start_point: 'LHR',
                final_destination: 'CDG',
                duration: '2h 30m',
                segments: [
                    {
                        id: 1,
                        departure_iata: 'LHR',
                        arrival_iata: 'CDG',
                        departure_time: '2025-05-02 09:00:00',
                        arrival_time: '2025-05-02 11:30:00',
                        duration: '2h 30m',
                    },
                ],
                number_of_seats: 9,
                price: '232.10',
                currency: 'EUR',
            },
        ];

        const wrapper = mount(FlightTable, {
            props: { title: 'Test Flights', flights, showActions: false },
        });

        expect(wrapper.text()).toContain('Test Flights');
        expect(wrapper.text()).toContain('LHR');
        expect(wrapper.text()).toContain('CDG');
        expect(wrapper.text()).toContain('2h 30m');
        expect(wrapper.text()).toContain('232.10');
        expect(wrapper.text()).toContain('EUR');
    });
});
