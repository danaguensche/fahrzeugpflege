<template>
    <WidgetLayout title="Heute fällige Termine" :value="numberOfTodaysJobs" icon="mdi-calendar" color="orange lighten-4"
        outlined>
    </WidgetLayout>
</template>

<script>
import WidgetLayout from './WidgetLayout.vue';
import axios from 'axios';
export default {
    name: 'TodaysJobsWidget',
    components: {
        WidgetLayout
    },
    data() {
        return {
            numberOfTodaysJobs: 0,
        };
    },
    mounted() {
        this.getNumberOfTodaysJobs();
    },
    methods: {
        async getNumberOfTodaysJobs() {
            await axios.get('/api/jobs/countjobstoday')
                .then(response => {
                    this.numberOfTodaysJobs = response.data.count;
                })
                .catch(error => {
                    console.error('Error fetching number of today\'s jobs:', error);
                });
        }
    }
}
</script>