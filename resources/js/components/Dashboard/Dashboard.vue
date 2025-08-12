<template>
    <div class="dashboard-page" :class="{ 'dashboard-page-sidebar-opened': isSidebarOpen }">
        <v-container class="dashboard-container" fluid>

            <v-row class="mb-6">
                <v-col cols="12">
                    <v-card elevation="2" class="widgets-card">
                        <v-card-text class="pt-4">
                            <v-row dense>
                                <v-col cols="12" sm="6" md="3">
                                    <OpenJobsWidget />
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <TodaysJobsWidget />
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <CustomersWidget />
                                </v-col>
                                <v-col cols="12" sm="6" md="3">
                                    <CarsWidget />
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="6">
                    <CalendarWidget />
                </v-col>

                <v-col cols="12" md="6">
                    <v-card elevation="2" class="h-100">
                        <v-card-title class="text-h6 pb-2">
                            <v-icon left color="primary">mdi-timeline</v-icon>
                             |   Letzte Aktivitäten
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="pt-4">
                            <LastActivitiesWidget />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-card elevation="2" class="actions-card">
                        <v-card-title class="text-h6 pb-2">
                            <v-icon class="mr-2" color="primary">mdi-lightning-bolt</v-icon>
                            Schnellaktionen
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="pt-4">
                            <v-row dense>
                                <v-col cols="12" sm="6" md="4">
                                    <v-btn block color="primary" @click="openAddCarDialog" prepend-icon="mdi-plus"
                                        size="large" class="mb-2">
                                        Fahrzeug hinzufügen
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-btn block color="secondary" @click="openAddCustomerDialog"
                                        prepend-icon="mdi-account-plus" size="large" class="mb-2">
                                        Kunde hinzufügen
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-btn block color="success" @click="openAddJobDialog"
                                        prepend-icon="mdi-briefcase-plus" size="large" class="mb-2">
                                        Auftrag hinzufügen
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <AddCarForm v-model="showAddCarDialog" @car-added="handleCarAdded" />
            <AddCustomerForm v-model="showAddCustomerDialog" @customer-added="handleCustomerAdded" />
            <AddJobForm v-model="showAddJobDialog" @job-added="handleJobAdded" />

        </v-container>
    </div>
</template>

<script>
import AddCarForm from '../Cars/addCar/AddCarForm.vue';
import AddCustomerForm from '../Customers/addCustomer/AddCustomerForm.vue';
import AddJobForm from '../Jobs/AddJobForm.vue';
import { mapState, mapGetters } from 'vuex';
import OpenJobsWidget from './Widgets/OpenJobsWidget.vue';
import WidgetLayout from './Widgets/WidgetLayout.vue';
import axios from 'axios';
import CarsWidget from './Widgets/CarsWidget.vue';
import CustomersWidget from './Widgets/CustomersWidget.vue';
import TodaysJobsWidget from './Widgets/TodaysJobsWidget.vue';
import CalendarWidget from './Widgets/CalendarWidget.vue';
import LastActivitiesWidget from './Widgets/LastActivitiesWidget.vue';

export default {
    name: 'Dashboard',
    components: {
        WidgetLayout,
        OpenJobsWidget,
        CarsWidget,
        CustomersWidget,
        TodaysJobsWidget,
        AddCarForm,
        AddCustomerForm,
        AddJobForm,
        CalendarWidget,
        LastActivitiesWidget
    },
    data() {
        return {
            numberOfCars: 0,
            showAddCarDialog: false,
            showAddCustomerDialog: false,
            showAddJobDialog: false,
        };
    },

    computed: {
        ...mapState(['isSidebarOpen']),
        ...mapGetters('auth', ['isAdminOrTrainer']),
    },

    methods: {
        openAddCarDialog() {
            this.showAddCarDialog = true;
        },
        handleCarAdded() {
            this.showAddCarDialog = false;
        },
        openAddCustomerDialog() {
            this.showAddCustomerDialog = true;
        },
        handleCustomerAdded() {
            this.showAddCustomerDialog = false;
        },
        openAddJobDialog() {
            this.showAddJobDialog = true;
        },
        handleJobAdded() {
            this.showAddJobDialog = false;
        },
    }
}
</script>

<style scoped>
.dashboard-page {
    display: flex;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
    margin-left: 180px;
    /* min-height: 100vh; */
    /* background-color: #f5f5f5; */
    border-radius: 12px;
}

.dashboard-page-sidebar-opened {
    margin-left: 330px;
}

.dashboard-container {
    align-self: flex-start;
    margin: 0 auto;
    padding: 32px 32px;
    border-radius: 12px;
}

.widgets-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 12px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.actions-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 12px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.h-100 {
    height: 100%;
    width: 100%;
}

.v-card {
    border-radius: 12px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.v-card-title {
    font-weight: 600;
    color: #2c3e50;
}

.v-btn {
    border-radius: 8px !important;
    font-weight: 500;
    text-transform: none;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.v-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

/* Tablet Styles */
@media only screen and (max-width: 1024px) {
    .dashboard-page-sidebar-opened {
        margin-left: 150px;
    }

    .dashboard-container {
        padding: 24px 16px;
    }
}

/* Mobile Styles */
@media only screen and (max-width: 768px) {
    .dashboard-page {
        margin-left: 0;
    }

    .dashboard-page-sidebar-opened {
        margin-left: 0;
    }

    .dashboard-container {
        padding: 16px 12px;
    }

    .v-btn {
        margin-bottom: 8px;
    }
}

/* Small mobile adjustments */
@media only screen and (max-width: 480px) {
    .dashboard-container {
        padding: 12px 8px;
    }
}
</style>