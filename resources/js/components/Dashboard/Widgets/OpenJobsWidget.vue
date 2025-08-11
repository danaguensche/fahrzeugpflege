<template>
    <WidgetLayout title="Ausstehende Aufträge" :value="numberOfOpenJobs" icon="mdi-briefcase" color="blue lighten-4" outlined>
    </WidgetLayout>
</template>

<script>
import WidgetLayout from './WidgetLayout.vue';
export default {
    name: 'OpenJobsWidget',
    components: {
        WidgetLayout
    },
    data() {
        return {
            numberOfOpenJobs: 0,
        };
    },
    mounted() {
        this.getNumberOfOpenJobs();
        setInterval(() => {
            this.getNumberOfOpenJobs();
        }, 60000);
    },
    methods: {
        getNumberOfOpenJobs() {
            axios.get('/api/jobs/openjobs')
                .then(response => {
                    this.numberOfOpenJobs = response.data.count;
                })
                .catch(error => {
                    console.error('Error fetching number of open jobs:', error);
                });
        }
    }
}
</script>