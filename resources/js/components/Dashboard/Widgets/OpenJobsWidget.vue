<template>
    <WidgetLayout title="Ausstehende Aufträge" :value="numberOfOpenJobs" icon="mdi-briefcase" color="blue lighten-4" :link="'/auftraege'" outlined>
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

    },
    methods: {
        async getNumberOfOpenJobs() {
            await axios.get('/api/jobs/openjobs')
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