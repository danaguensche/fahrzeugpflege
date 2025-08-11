<template>
    <WidgetLayout title="Fahrzeuge in Pflege" :value="numberOfCars" icon="mdi-car" color="green lighten-4" />
</template>

<script>
import WidgetLayout from './WidgetLayout.vue';
import axios from 'axios';
export default {
    name: 'CarsWidget',
    components: {
        WidgetLayout
    },
    data() {
        return {
            numberOfCars: 0,
        };
    },
    mounted() {
        this.getNumberOfCars();
    },

    methods: {
        async getNumberOfCars() {
            await axios.get('/api/cars/countcars')
                .then(response => {
                    this.numberOfCars = response.data.count;
                })
                .catch(error => {
                    console.error('Error fetching number of cars:', error);
                });
        }
    }
}
</script>