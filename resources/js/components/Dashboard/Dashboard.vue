<template>
    <div class="dashboard-page" :class="{ 'dashboard-page-sidebar-opened': isSidebarOpen }">
        <v-container class="dashboard-container" fluid>

            <!-- Row 1 Widgets -->
            <v-row class="widgets-row">
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

            <!-- Row 2 Calendar & Activities -->
            <div class="content-row">
                <div class="v-col">
                    <CalendarWidget />
                </div>

                <div class="v-col">
                    <v-card elevation="2" class="h-100">
                        <v-card-title class="text-h6 pb-2">
                            <v-icon left color="primary">mdi-timeline</v-icon>
                            | Letzte Aktivitäten
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="pt-4">
                            <LastActivitiesWidget />
                        </v-card-text>
                    </v-card>
                </div>
            </div>

            <!-- Row 3 Actions -->
            <v-row class="actions-row">
                <v-col cols="12">
                    <v-card elevation="2" class="actions-card">
                        <v-card-title class="text-h6 pb-2">
                            <v-icon class="mr-2" color="primary">mdi-lightning-bolt</v-icon>
                            Schnellaktionen
                        </v-card-title>
                        <v-card-text>
                            <v-row dense>
                                <v-col cols="12" sm="6" md="4">
                                    <v-btn block color="blue lighten-4" @click="openAddJobDialog"
                                        prepend-icon="mdi-briefcase-plus" size="large" class="mb-2">
                                        Auftrag hinzufügen
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-btn block color="green lighten-4" @click="openAddCarDialog"
                                        prepend-icon="mdi-plus" size="large" class="mb-2">
                                        Fahrzeug hinzufügen
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" sm="6" md="4">
                                    <v-btn block color="teal-lighten-2" @click="openAddCustomerDialog"
                                        prepend-icon="mdi-account-plus" size="large" class="mb-2">
                                        Kunde hinzufügen
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
    flex-direction: column;
    transition: margin-left 0.3s ease;
    font-family: var(--font-family);
    margin-left: 150px;
    height: 100vh;
    overflow: hidden;
}

.dashboard-page-sidebar-opened {
    margin-left: 320px;
}

.dashboard-container {
    display: grid;
    grid-template-rows: auto 1fr auto;
    height: 100vh;
    padding: 16px;
    gap: 16px;
    overflow: hidden;
}

.widgets-row {
    min-height: 0;
}

.widgets-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 12px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.content-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    min-height: 0;
    height: 100%;
    margin: 0 -12px;
}

.content-row .v-col {
    height: 100%;
    min-height: 0;
}

.h-100 {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.h-100 .v-card-text {
    flex: 1;
    overflow-y: auto;
    min-height: 0;
}

.actions-row {
    min-height: 0;
    margin-bottom: 20px;
}

.actions-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 12px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.v-card {
    border-radius: 12px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    height: 100%;
}

/* Tablet */
@media only screen and (max-width: 1024px) {
    .dashboard-page-sidebar-opened {
        margin-left: 150px;
    }

    .content-row {
        grid-template-columns: 1fr;
    }

    .dashboard-container {
        padding: 12px;
        gap: 12px;
    }
}

/* Mobile */
@media only screen and (max-width: 768px) {
    .dashboard-page {
        margin-left: 0;
        height: 100dvh;
    }

    .dashboard-page-sidebar-opened {
        margin-left: 0;
    }

    .dashboard-container {
        height: 100dvh;
        padding: 8px;
        gap: 8px;
        overflow-y: auto;
        grid-template-rows: auto auto auto;
    }

    .content-row {
        display: flex;
        flex-direction: column;
        height: auto;
    }
}
</style>