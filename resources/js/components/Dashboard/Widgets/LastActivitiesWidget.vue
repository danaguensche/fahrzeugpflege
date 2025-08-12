<template>
    <v-card>
        <v-list>
            <v-list-item v-for="a in activities" :key="a.id">
                <v-list-item-content>
                    <v-list-item-title>{{ a.description }}</v-list-item-title>
                    <v-list-item-subtitle>
                        {{ a.causer?.firstname }} – {{ formatDate(a.created_at) }}
                    </v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-card>
</template>

<script>
import axios from 'axios';

export default {
    name: 'LastActivitiesWidget',
    data() {
        return { activities: [] }
    },
    mounted() {
        this.getActivities();
    },

    methods: {
        formatDate(date) {
            return new Date(date).toLocaleString();
        },
        async getActivities() {

            try {
                const response = await axios.get('/api/activities');
                this.activities = response.data.slice(0, 5); // Limit to last 5 activities

            } catch (error) {
                console.error('Error fetching activities:', error);
            }

        }
    }
}
</script>