<template>
    <div class="today-widget fixed-height">
        <div class="widget-header">
            <h3>Heute - {{ formatDate(today) }}</h3>
            <div class="event-count-badge">{{ todayEvents.length }}</div>
        </div>

        <div class="widget-content">
            <div v-if="todayEvents.length === 0" class="no-events">
                <div class="no-events-icon">📅</div>
                <p>Keine Termine heute</p>
            </div>

            <div v-else class="events-list">
                <div v-for="event in todayEvents" :key="event.id" class="event-item"
                    :class="[event.status.replace(/_/g, '-'), event.type]" @click="onEventClick(event)">
                    <div class="event-time">
                        <div v-if="event.type === 'cleaning'">
                            {{ formatTime(event.start) }} - {{ formatTime(event.end) }}
                        </div>
                        <div v-else>
                            {{ formatTime(event.start) }}
                        </div>
                    </div>
                    <div class="event-details">
                        <div class="event-title">
                            {{ event.title }}
                            <span v-if="event.type === 'cleaning'" class="event-type-badge cleaning">Reinigung</span>
                            <span v-else class="event-type-badge pickup">Abholung</span>
                        </div>
                        <div class="event-customer">
                            {{ event.customer_firstname }} {{ event.customer_lastname }}
                        </div>
                        <div class="event-car" v-if="event.car_kennzeichen !== 'N/A'">
                            {{ event.car_make }} {{ event.car_model }} ({{ event.car_kennzeichen }})
                        </div>
                    </div>
                    <div class="event-status">
                        <span class="status-dot" :class="event.status.replace(/_/g, '-')"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kompakte Legende -->
        <div class="widget-legend">
            <div class="legend-item">
                <span class="legend-dot ausstehend"></span>
                <span>Ausstehend</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot in-bearbeitung"></span>
                <span>In Bearbeitung</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot abgeschlossen"></span>
                <span>Abgeschlossen</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot im-rueckblick"></span>
                <span>Rückblick</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot pickup"></span>
                <span>Abholung</span>
            </div>
        </div>

        <!-- Event Dialog -->
        <EventDialog :event="selectedEvent" :visible="eventDialog" @close="closeEventDialog" />
    </div>
</template>

<script>
import axios from 'axios';
import EventDialog from '../../Calendar/EventDialog.vue';
export default {
    name: 'CalendarWidget',
    components: {
        EventDialog
    },
    data() {
        return {
            events: [],
            selectedEvent: null,
            eventDialog: false,
            today: new Date(),
            refreshInterval: null
        };
    },
    computed: {
        todayEvents() {
            const todayStr = this.today.toISOString().slice(0, 10);
            return this.events.filter(event => {
                const eventDate = new Date(event.start).toISOString().slice(0, 10);
                return eventDate === todayStr;
            }).sort((a, b) => {
                const timeA = new Date(a.start);
                const timeB = new Date(b.start);
                return timeA - timeB;
            });
        }
    },
    mounted() {
        this.fetchTodayEvents();
        // Aktualisiere alle 5 Minuten
        this.refreshInterval = setInterval(() => {
            this.fetchTodayEvents();
        }, 5 * 60 * 1);
    },
    beforeDestroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    },
    methods: {
        fetchTodayEvents() {
            const startOfDay = new Date(this.today);
            startOfDay.setHours(0, 0, 0, 0);

            const endOfDay = new Date(this.today);
            endOfDay.setHours(23, 59, 59, 999);

            axios.get('/api/jobs', {
                params: {
                    start: startOfDay.toISOString().slice(0, 10) + ' 00:00:00',
                    end: endOfDay.toISOString().slice(0, 10) + ' 23:59:59',
                },
            })
                .then(response => {
                    this.events = [];

                    response.data.items.forEach(job => {
                        if (job.cleaning_start && job.cleaning_end) {
                            const cleaningStart = new Date(job.cleaning_start);
                            const cleaningEnd = new Date(job.cleaning_end);

                            this.events.push({
                                id: `${job.id}-cleaning`,
                                start: cleaningStart.toISOString(),
                                end: cleaningEnd.toISOString(),
                                title: job.title,
                                content: job.description,
                                status: job.status,
                                type: 'cleaning',
                                email: job.customer ? job.customer.email : 'N/A',
                                customer_firstname: job.customer ? job.customer.firstname : 'N/A',
                                customer_lastname: job.customer ? job.customer.lastname : 'N/A',
                                customer_id: job.customer ? job.customer.id : null,
                                car_kennzeichen: job.car ? job.car.Kennzeichen : 'N/A',
                                car_make: job.car ? job.car.make : 'N/A',
                                car_model: job.car ? job.car.model : 'N/A',
                                services_list: job.services ? job.services.map(service => service.title) : [],
                                job_id: job.id,
                                class: job.status.replace(/_/g, '-'),
                            });
                        }

                        if (job.scheduled_at) {
                            const scheduledAt = new Date(job.scheduled_at);
                            const scheduledEnd = new Date(scheduledAt.getTime() + 30 * 60 * 1000); // 30 Minuten später

                            this.events.push({
                                id: `${job.id}-pickup`,
                                start: scheduledAt.toISOString(),
                                end: scheduledEnd.toISOString(),
                                title: job.title,
                                content: job.description,
                                status: job.status,
                                type: 'pickup',
                                email: job.customer ? job.customer.email : 'N/A',
                                customer_firstname: job.customer ? job.customer.firstname : 'N/A',
                                customer_lastname: job.customer ? job.customer.lastname : 'N/A',
                                customer_id: job.customer ? job.customer.id : null,
                                car_kennzeichen: job.car ? job.car.Kennzeichen : 'N/A',
                                car_make: job.car ? job.car.make : 'N/A',
                                car_model: job.car ? job.car.model : 'N/A',
                                services_list: job.services ? job.services.map(service => service.title) : [],
                                job_id: job.id,
                                class: job.status.replace(/_/g, '-'),
                            });
                        }
                    });
                })
                .catch(error => {
                    console.error("Error fetching today's events:", error);
                });
        },

        onEventClick(event) {
            this.selectedEvent = event;
            this.eventDialog = true;
        },

        closeEventDialog() {
            this.eventDialog = false;
            this.selectedEvent = null;
        },

        formatDate(date) {
            if (!date) return 'Unbekannt';
            try {
                return new Intl.DateTimeFormat('de-DE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                }).format(new Date(date));
            } catch {
                return 'Ungültiges Datum';
            }
        },

        formatTime(dateTimeString) {
            if (!dateTimeString) return 'Unbekannt';
            try {
                return new Intl.DateTimeFormat('de-DE', {
                    hour: '2-digit',
                    minute: '2-digit',
                    timeZone: 'Europe/Berlin'
                }).format(new Date(dateTimeString));
            } catch {
                return 'Ungültiges Datum';
            }
        },
    },

}

</script>

<style scoped>
.today-widget {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 20px;
    font-family: 'Rubik', sans-serif;

}

.fixed-height {
    height: 380px;
    overflow: hidden;
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.widget-header h3 {
    margin: 0;
    color: #212529;
    font-size: 1.2em;
    font-weight: 600;
}

.event-count-badge {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-radius: 50%;
    width: 32px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9em;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
}

.widget-content {
    margin-bottom: 20px;
}

.no-events {
    text-align: center;
    padding: 30px 20px;
    color: #6c757d;
}

.no-events-icon {
    font-size: 3em;
    margin-bottom: 15px;
}

.no-events p {
    margin: 0;
    font-size: 1.1em;
    font-weight: 500;
}

.events-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    height: 200px;
    overflow-y: auto;
}

.event-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    border-left: 4px solid transparent;
}

.event-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.event-item.ausstehend {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    border-left-color: #ff9800;
}

.event-item.in-bearbeitung {
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
    border-left-color: #00bcd4;
}

.event-item.abgeschlossen {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    border-left-color: #4caf50;
}

.event-item.im-rueckblick {
    background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
    border-left-color: #e9c455;
}

.event-item.pickup {
    border-left: 4px solid #9c27b0;
    background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
}

.event-time {
    font-weight: 700;
    font-size: 1.1em;
    color: #212529;
    min-width: 80px;
    text-align: center;
}

.event-details {
    flex: 1;
    z-index: 1;
}

.event-title {
    font-weight: 600;
    font-size: 1em;
    color: #212529;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.event-type-badge {
    font-size: 0.7em;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.event-type-badge.cleaning {
    background-color: #e3f2fd;
    color: #1976d2;
}

.event-type-badge.pickup {
    background-color: #f3e5f5;
    color: #7b1fa2;
}

.event-customer {
    font-size: 0.9em;
    color: #495057;
    margin-bottom: 2px;
}

.event-car {
    font-size: 0.8em;
    color: #6c757d;
    font-style: italic;
}

.event-status {
    display: flex;
    align-items: center;
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.status-dot.ausstehend {
    background: #ff9800;
}

.status-dot.in-bearbeitung {
    background: #00bcd4;
}

.status-dot.abgeschlossen {
    background: #4caf50;
}

.status-dot.im-rueckblick {
    background: #e9c455;
}

.widget-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    margin-top: 20px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8em;
    color: #495057;
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.legend-dot.ausstehend {
    background: #ff9800;
}

.legend-dot.in-bearbeitung {
    background: #00bcd4;
}

.legend-dot.abgeschlossen {
    background: #4caf50;
}

.legend-dot.im-rueckblick {
    background: #e9c455;
}

.legend-dot.pickup {
    background: #9c27b0;
}


/* Responsive Design */
@media (max-width: 480px) {
    .today-widget {
        padding: 15px;
        max-width: 100%;
    }

    .widget-header h3 {
        font-size: 1.1em;
    }

    .event-item {
        padding: 12px;
        gap: 10px;
    }

    .event-time {
        min-width: 70px;
        font-size: 0.9em;
    }

    .widget-legend {
        gap: 10px;
    }
}
</style>