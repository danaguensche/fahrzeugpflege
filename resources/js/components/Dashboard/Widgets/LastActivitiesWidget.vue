<template>

    <v-card-text class="pt-0 widget-content">
        <div v-if="activities.length === 0" class="text-center py-4 text--secondary">
            Keine Aktivitäten vorhanden
        </div>

        <div v-else>
            <v-card v-for="(activity, index) in activities" :key="activity.id" class="activity-item mb-3" outlined
                hover>
                <v-card-text class="py-3">
                    <div class="activity-description mb-1">
                        {{ activity.description }}
                    </div>
                    <div class="activity-meta d-flex align-center">
                        <v-avatar size="24" class="mr-2" color="grey lighten-3">
                            <span class="caption">
                                {{ getInitials(activity.causer?.firstname) }}
                            </span>
                        </v-avatar>
                        <span class="text--secondary caption">
                            {{ activity.causer?.firstname }} • {{ formatDate(activity.created_at) }}
                        </span>
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-card-text>

</template>

<script>
import axios from 'axios';

export default {
    name: 'LastActivitiesWidget',
    data() {
        return {
            activities: [],
            loading: false
        }
    },
    mounted() {
        this.getActivities();
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        getInitials(name) {
            if (!name) return '?';
            return name.charAt(0).toUpperCase();
        },
        async getActivities() {
            this.loading = true;
            try {
                const response = await axios.get('/api/activities');
                this.activities = response.data.slice(0, 5); // Limit to last 5 activities
            } catch (error) {
                console.error('Error fetching activities:', error);
                this.$toast?.error('Fehler beim Laden der Aktivitäten');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>

.widget-content {
 height: 300px;
 overflow-y: auto;
}

.activity-item {
    transition: all 0.2s ease;
    border-radius: 8px !important;
}

.activity-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12) !important;
}

.activity-item:last-child {
    margin-bottom: 0 !important;
}

.activity-description {
    font-weight: 500;
    line-height: 1.3;
    color: rgba(0, 0, 0, 0.87);
}

.activity-meta {
    font-size: 0.75rem;
}

.v-card-title {
    font-size: 1.1rem;
    font-weight: 600;
}
</style>