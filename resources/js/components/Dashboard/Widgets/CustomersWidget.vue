<template>
    <WidgetLayout title="Neue Kunden diesen Monat" :value="numberOfCustomersOneMonth" icon="mdi-account-plus" ></WidgetLayout>
</template>

<script>
import WidgetLayout from './WidgetLayout.vue';
import axios from 'axios';

export default {
    name: 'CustomersWidget',
    components: {
        WidgetLayout
    },
    data() {
        return {
            numberOfCustomersOneMonth: 0,
        };
    },
    mounted() {
        this.getNumberOfCustomers();
        setInterval(() => {
            this.getNumberOfCustomers();
        }, 60000); // Update every 60 seconds
    },
    methods: {
        getNumberOfCustomers() {
            axios.get('/api/customers/customerscurrentmonth')
                .then(response => {
                    this.numberOfCustomersOneMonth = response.data.count;
                })
                .catch(error => {
                    console.error('Error fetching number of new customers:', error);
                });
        }
    }
}
</script>