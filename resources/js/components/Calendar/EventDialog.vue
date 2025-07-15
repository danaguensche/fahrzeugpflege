<template>
    <v-dialog v-model="dialogVisible" max-width="800px" persistent>
        <v-card v-if="event" class="event-details elevation-10">
            <v-card-title class="headline pa-6 pb-4 primary white--text">
                <span class="text-h5 font-weight-bold">{{ event.title }}</span>
                <v-spacer></v-spacer>
            </v-card-title>

            <v-card-text class="pa-0">
                <v-list class="bg-transparent">
                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="primary">mdi-information</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title class="font-weight-medium">Status</v-list-item-title>
                            <v-list-item-subtitle>
                                <v-chip :class="getStatusChipClass(event.status)" text-color="white" small>
                                    {{ getStatusText(event.status) }}
                                </v-chip>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider></v-divider>

                    <!-- Beschreibung -->
                    <v-list-item v-if="event.content">
                        <v-list-item-icon>
                            <v-icon color="primary">mdi-text</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title class="font-weight-medium">Beschreibung</v-list-item-title>
                            <v-list-item-subtitle class="text-wrap">
                                {{ event.content }}
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider v-if="event.content"></v-divider>

                    <!-- Kunde -->
                    <v-list-item v-if="event.customer_id">
                        <v-list-item-icon>
                            <v-icon color="primary">mdi-account</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title class="font-weight-medium">Kunde</v-list-item-title>
                            <v-list-item-subtitle>
                                <router-link :to="'/kunden/kundendetails/' + event.customer_id"
                                    class="text-decoration-none primary--text font-weight-medium">
                                    {{ event.customer_firstname }} {{ event.customer_lastname }}
                                </router-link>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider v-if="event.customer_id"></v-divider>

                    <!-- E-Mail -->
                    <v-list-item v-if="event.email && event.email !== 'N/A'">
                        <v-list-item-icon>
                            <v-icon color="primary">mdi-email</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title class="font-weight-medium">E-Mail</v-list-item-title>
                            <v-list-item-subtitle>
                                <a :href="'mailto:' + event.email" class="text-decoration-none primary--text">
                                    {{ event.email }}
                                </a>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider v-if="event.email && event.email !== 'N/A'"></v-divider>

                    <!-- Fahrzeug -->
                    <v-list-item v-if="event.car_kennzeichen && event.car_kennzeichen !== 'N/A'">
                        <v-list-item-icon>
                            <v-icon color="primary">mdi-car</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title class="font-weight-medium">Fahrzeug</v-list-item-title>
                            <v-list-item-subtitle>
                                <router-link :to="'/fahrzeuge/fahrzeugdetails/' + event.car_kennzeichen"
                                    class="text-decoration-none primary--text font-weight-medium">
                                    {{ event.car_kennzeichen }}
                                </router-link>
                                <span v-if="event.car_make && event.car_model && event.car_make !== 'N/A'">
                                    - {{ event.car_make }} {{ event.car_model }}
                                </span>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider v-if="event.car_kennzeichen && event.car_kennzeichen !== 'N/A'"></v-divider>

                    <!-- Services -->
                    <v-list-item v-if="event.services_list && event.services_list.length > 0">
                        <v-list-item-icon>
                            <v-icon color="primary">mdi-cog</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title class="font-weight-medium">Services</v-list-item-title>
                            <v-list-item-subtitle>
                                <div class="mt-2">
                                    <v-chip v-for="(service, index) in event.services_list" :key="index" class="ma-1"
                                        color="grey lighten-4" text-color="primary" small outlined>
                                        <v-icon left small>mdi-wrench</v-icon>
                                        {{ service }}
                                    </v-chip>
                                </div>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>

                    <v-divider v-if="event.services_list && event.services_list.length > 0"></v-divider>

                    <!-- Zeitraum -->
                    <v-list-item>
                        <v-list-item-icon>
                            <v-icon color="primary">mdi-clock</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title class="font-weight-medium">Zeitraum</v-list-item-title>
                            <v-list-item-subtitle>
                                <div class="d-flex align-center flex-wrap">
                                    <div class="d-flex align-center mr-4 mb-1">
                                        <v-icon small color="success" class="mr-2">mdi-play</v-icon>
                                        <span>{{ formatDateTime(event.start) }}</span>
                                    </div>
                                    <div class="d-flex align-center">
                                        <v-icon small color="error" class="mr-2">mdi-stop</v-icon>
                                        <span>{{ formatDateTime(event.end) }}</span>
                                    </div>
                                </div>
                            </v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-card-text>

            <v-card-actions class="pa-6 pt-4">
                <v-btn color="primary" :to="'/auftraege/jobdetails/' + event.job_id" class="mr-2" variant="outlined">
                    <v-icon left>mdi-open-in-new</v-icon>
                    Details anzeigen
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn color="grey darken-1" text @click="closeDialog" variant="outlined">
                    Schließen
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'EventDialog',
    props: {
        event: {
            type: Object,
            default: null
        },
        visible: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        dialogVisible: {
            get() {
                return this.visible;
            },
            set(value) {
                if (!value) {
                    this.closeDialog();
                }
            }
        }
    },
    methods: {
        closeDialog() {
            this.$emit('close');
        },

        getStatusChipClass(status) {
            const statusClasses = {
                'ausstehend': 'orange',
                'in-bearbeitung': 'cyan',
                'abgeschlossen': 'green'
            };
            return statusClasses[status] || 'grey';
        },

        getStatusText(status) {
            const statusTexts = {
                'ausstehend': 'Ausstehend',
                'in-bearbeitung': 'In Bearbeitung',
                'abgeschlossen': 'Abgeschlossen'
            };
            return statusTexts[status] || status;
        },

        formatDateTime(dateString) {
            if (!dateString) return '';

            try {
                if (dateString.format) {
                    return dateString.format('DD.MM.YYYY HH:mm');
                }

                let date;
                if (typeof dateString === 'string') {
                    // Format: "2024-01-15 14:30" -> "2024-01-15T14:30"
                    date = new Date(dateString.replace(' ', 'T'));
                } else {
                    date = new Date(dateString);
                }

                if (isNaN(date.getTime())) {
                    return dateString;
                }

                return date.toLocaleDateString('de-DE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                console.error('Error formatting date:', error);
                return dateString;
            }
        }
    }
}
</script>

<style scoped>
/* Event Details Styles */

.event-details {
    padding: 30px;
    border-radius: 12px !important;
    overflow: hidden;
}

.event-details .v-card__title {
    background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
}

.v-list-item {
    min-height: 60px;
    padding: 12px 24px;
    transition: background-color 0.2s ease;
}

.v-list-item:hover {
    background-color: #f5f5f5;
}

.v-list-item__icon {
    margin-right: 16px;
}

.v-list-item__title {
    color: #2c3e50;
    margin-bottom: 4px;
    font-size: 0.95rem;
}

.v-list-item__subtitle {
    color: #546e7a;
    line-height: 1.4;
}

.text-wrap {
    white-space: normal !important;
    word-wrap: break-word;
}

.v-chip {
    font-weight: 500;
    transition: all 0.2s ease;
}

.v-chip:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.v-divider {
    margin: 0 24px;
    opacity: 0.3;
}

.v-card__actions {
    background-color: #fafafa;
    border-top: 1px solid #e0e0e0;
}

.v-btn {
    transition: all 0.2s ease;
}

.v-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Link Styles */
a {
    transition: all 0.2s ease;
}

a:hover {
    text-decoration: underline !important;
}

/* Responsive Design */
@media (max-width: 600px) {
    .event-details {
        margin: 0;
        border-radius: 0 !important;
        max-height: 90vh;
        overflow-y: auto;
    }

    .v-list-item {
        padding: 8px 16px;
        min-height: 48px;
    }

    .v-card__title {
        padding: 16px !important;
    }

    .v-card__actions {
        padding: 16px !important;
        flex-direction: column;
        gap: 8px;
    }

    .v-card__actions .v-btn {
        width: 100%;
    }

    .d-flex.align-center {
        flex-direction: column;
        align-items: flex-start !important;
    }

    .d-flex.align-center .mr-4 {
        margin-right: 0 !important;
        margin-bottom: 8px;
    }
}

@media (max-width: 400px) {
    .v-card__title .text-h5 {
        font-size: 1.1rem !important;
    }

    .v-list-item__title {
        font-size: 0.9rem;
    }

    .v-list-item__subtitle {
        font-size: 0.8rem;
    }
}
</style>