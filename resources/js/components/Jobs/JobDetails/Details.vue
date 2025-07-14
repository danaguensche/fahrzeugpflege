<template>
    <v-container class="card-container">
        <!-- Skeleton Loader wenn loading true ist -->
        <Loader v-if="loading"></Loader>

        <!-- Vollständige Ansicht der Daten wenn loading false ist -->
        <template v-else>
            <!-- Header -->
            <v-card class="card">
                <Header :title="headerTitle" :switchEditMode="switchEditMode" :icon="headerIcon"></Header>

                <!-- Job information -->
                <v-card-text class="px-4 pt-4 pb-0">
                    <v-sheet>
                        <InformationHeader :title="'Jobinformationen'" :editMode="editMode"
                            :getIconForField="getIconForField">
                        </InformationHeader>

                        <!-- Ansichtsmodus -->
                        <InfoList v-if="!editMode" :details="displayedJobDetails" :labels="labels"
                            :infoKeys="jobInfoKeys" :getIconForField="getIconForField">

                        </InfoList>
                        <!-- Bearbeitungsmodus -->
                        <div v-else>
                            <InfoListEditMode :personalInfoKeys="jobInfoKeys.filter(k => k !== 'Status' && k !== 'Abholtermin')" :labels="labels"
                                :editedData="editedJobData" @update:editedData="editedJobData = $event"
                                :getIconForField="getIconForField">
                            </InfoListEditMode>
                            <v-text-field
                                v-model="formattedAbholterminForEdit"
                                type="datetime-local"
                                :label="labels.Abholtermin"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                class="mt-4"
                                :prepend-inner-icon="getIconForField('Abholtermin')"
                                @keydown.prevent
                            ></v-text-field>
                            <v-select
                                v-model="editedJobData.Status"
                                :items="statuses"
                                item-title="title"
                                item-value="value"
                                label="Status"
                                variant="outlined"
                                density="comfortable"
                                hide-details="auto"
                                class="mt-4"
                                :prepend-inner-icon="getIconForField('Status')"
                            ></v-select>
                        </div>
                    </v-sheet>

                    <!-- Customer information -->
                    <v-sheet>
                        <DefaultHeader :title="'Kundeninformation'"></DefaultHeader>
                        <CustomerInfoList v-if="!editMode" :customer="jobDetails.data.customer"
                            :customerId="jobDetails.data.customer_id" :labels="labels">
                        </CustomerInfoList>

                        <v-autocomplete v-else v-model="editedJobData.customer" class="w-50 ms-4" :items="customers"
                            item-title="full_name" item-value="id" label="Kunde"
                            placeholder="Kunde auswählen oder suchen" prepend-inner-icon="mdi-account"
                            variant="outlined" density="comfortable" hide-details="auto" clearable
                            :loading="customersLoading" :search-input.sync="customerSearch"
                            @update:search-input="searchCustomers" return-object>
                            <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props" :title="`${item.raw.firstname} ${item.raw.lastname}`"
                                    :subtitle="item.raw.email"></v-list-item>
                            </template>
                            <template v-slot:selection="{ item }">
                                {{ item.raw.email }}
                            </template>
                        </v-autocomplete>
                    </v-sheet>

                    <!-- Car information -->
                    <v-sheet>
                        <DefaultHeader :title="'Fahrzeuginformationen'"></DefaultHeader>
                        <template v-if="!editMode">
                            <template v-if="!editMode">
                                <template v-if="jobDetails.data.car && jobDetails.data.car.Kennzeichen">
                                    <v-list class="bg-transparent">
                                        <template v-for="key in carInfoKeys" :key="key">
                                            <v-list-item
                                                v-if="jobDetails.data.car[key] !== undefined && jobDetails.data.car[key] !== null && jobDetails.data.car[key] !== ''">
                                                <template v-slot:prepend>
                                                    <v-icon :icon="getIconForField(key)" color="primary" class="mr-2">
                                                    </v-icon>
                                                </template>

                                                <v-list-item-title class="font-weight-medium">
                                                    {{ labels[key] || key }}
                                                </v-list-item-title>

                                                <v-list-item-subtitle class="mt-1 text-body-1">
                                                    <template v-if="key === 'Kennzeichen'">
                                                        <router-link
                                                            :to="`/fahrzeuge/fahrzeugdetails/${jobDetails.data.car.Kennzeichen}`"
                                                            class="text-decoration-none text-primary">
                                                            {{ jobDetails.data.car.Kennzeichen }}
                                                        </router-link>
                                                    </template>
                                                    <template v-else>
                                                        {{ jobDetails.data.car[key] }}
                                                    </template>
                                                </v-list-item-subtitle>
                                            </v-list-item>
                                            <v-divider
                                                v-if="key !== carInfoKeys[carInfoKeys.length - 1] && jobDetails.data.car[key] !== undefined && jobDetails.data.car[key] !== null && jobDetails.data.car[key] !== ''">
                                            </v-divider>
                                        </template>
                                    </v-list>
                                </template>
                                <template v-else>
                                    <v-list-item>
                                        <v-list-item-subtitle class="text-grey">
                                            <div class="d-flex align-center justify-center pa-4">
                                                <v-icon icon="mdi-car-off" color="grey-lighten-1" size="32"
                                                    class="mr-2">
                                                </v-icon>
                                                <span>Kein Fahrzeug zugeordnet</span>
                                            </div>
                                        </v-list-item-subtitle>
                                    </v-list-item>
                                </template>
                            </template>
                        </template>

                        <v-autocomplete v-else class="w-50 ms-4" v-model="editedJobData.car" :items="cars"
                            item-title="Kennzeichen" item-value="id" label="Fahrzeug"
                            placeholder="Fahrzeug auswählen oder suchen" prepend-inner-icon="mdi-car" variant="outlined"
                            density="comfortable" hide-details="auto" clearable :loading="carsLoading"
                            :search-input.sync="carSearch" @update:search-input="searchCars" return-object>
                            <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props" :title="item.raw.Kennzeichen"
                                    :subtitle="item.raw.Automarke"></v-list-item>
                            </template>
                            <template v-slot:selection="{ item }">
                                {{ item.raw.Kennzeichen }}
                            </template>
                        </v-autocomplete>
                    </v-sheet>

                    <!-- Services information -->
                    <v-sheet>
                        <DefaultHeader :title="'Dienstleistungen'"></DefaultHeader>
                        <div v-if="!editMode" class="d-flex flex-wrap align-center">
                            <v-chip v-for="service in jobDetails.data.services" :key="service.id" class="ma-1"
                                color="primary" label>
                                {{ service.name }}
                            </v-chip>
                            <span v-if="jobDetails.data.services.length === 0" class="text-grey ma-1">
                                Keine Dienstleistungen zugewiesen
                            </span>
                        </div>

                        <v-autocomplete v-else class="w-50 ms-4" v-model="editedJobData.services" :items="services"
                            item-title="name" item-value="id" label="Dienstleistungen"
                            placeholder="Dienstleistungen auswählen" prepend-inner-icon="mdi-briefcase"
                            variant="outlined" density="comfortable" hide-details="auto" multiple chips clearable
                            :loading="servicesLoading" return-object>
                            <template v-slot:chip="{ props, item }">
                                <v-chip v-bind="props" :text="item.raw.name"></v-chip>
                            </template>
                            <template v-slot:item="{ props, item }">
                                <v-list-item v-bind="props" :title="item.raw.name"></v-list-item>
                            </template>
                        </v-autocomplete>
                    </v-sheet>

                    <!-- Metadaten -->
                    <MetaData :labels="labels" :formattedCreatedAt="formattedCreatedAt"
                        :formattedUpdatedAt="formattedUpdatedAt">
                    </MetaData>
                </v-card-text>

                <v-card-actions class="pa-4">
                    <BackButton></BackButton>
                    <v-spacer></v-spacer>

                    <!-- Bearbeitungsmodus Aktionen -->
                    <template v-if="editMode">
                        <CancelButton :cancelEdit="cancelEdit"></CancelButton>
                        <SaveButton :saveData="saveJobData"></SaveButton>
                    </template>

                    <!-- Ansichtsmodus Aktionen -->
                    <template v-else>
                        <EditButton :switchEditMode="switchEditMode"></EditButton>
                        <PrintButton></PrintButton>
                    </template>
                </v-card-actions>
            </v-card>
        </template>

        <!-- Snackbar für Benachrichtigungen -->
        <SnackBar v-if="snackbar.show" :text="snackbar.text" :color="snackbar.color" @close="snackbar.show = false">
        </SnackBar>
    </v-container>
</template>

<script>
import axios from "axios";
import Loader from "../../Details/Loader.vue";
import InformationHeader from "../../Details/InformationHeader.vue";
import Header from "../../Details/Header.vue";
import BackButton from "../../CommonSlots/BackButton.vue";
import SnackBar from "../../Details/SnackBar.vue";
import PrintButton from "../../CommonSlots/PrintButton.vue";
import EditButton from "../../Details/EditButton.vue";
import MetaData from "../../Details/MetaData.vue";
import CancelButton from "../../Details/CancelButton.vue";
import SaveButton from "../../Details/SaveButton.vue";
import InfoList from "../../Details/InfoList.vue";
import InfoListEditMode from "../../Details/InfoListEditMode.vue";
import DefaultHeader from "../../Details/DefaultHeader.vue";
import CustomerInfoList from "../../Details/CustomerInfoList.vue";

export default {
    name: "JobDetails",
    components: {
        Loader,
        InformationHeader,
        Header,
        BackButton,
        SnackBar,
        PrintButton,
        EditButton,
        MetaData,
        CancelButton,
        SaveButton,
        InfoList,
        InfoListEditMode,
        DefaultHeader,
        CustomerInfoList,
    },
    data() {
        return {
            jobDetails: {
                data: {
                    customer: null,
                    car: null,
                    services: [],
                }
            },
            editedJobData: {},
            headerTitle: "Jobdetails",
            headerIcon: "mdi-briefcase",
            labels: {
                id: "ID",
                Title: "Titel",
                Beschreibung: "Beschreibung",
                Abholtermin: "Abholtermin",
                Status: "Status",
                customer_id: "Kunden-ID",
                car_id: "Fahrzeug-ID",
                customer: "Kunde",
                car: "Fahrzeug",
                services: "Dienstleistungen",
                Kennzeichen: "Kennzeichen",
                Automarke: "Automarke",
                Typ: "Typ",
                Farbe: "Farbe",
                Sonstiges: "Sonstiges",
                created_at: "Erstellt am",
                updated_at: "Zuletzt aktualisiert am",
            },
            loading: true,
            error: null,
            editMode: false,
            saveLoading: false,
            customers: [],
            customersLoading: false,
            customerSearch: null,
            customerSearchTimeout: null,
            cars: [],
            carsLoading: false,
            carSearch: null,
            carSearchTimeout: null,
            services: [],
            servicesLoading: false,
            statuses: [
                { title: 'Ausstehend', value: 'ausstehend' },
                { title: 'In Bearbeitung', value: 'in_bearbeitung' },
                { title: 'Abgeschlossen', value: 'abgeschlossen' },
            ],
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
        };
    },
    computed: {
        jobInfoKeys() {
            return ['id', 'Title', 'Beschreibung', 'Abholtermin', 'Status'];
        },
        carInfoKeys() {
            return ['Kennzeichen', 'Automarke', 'Typ', 'Farbe', 'Sonstiges'];
        },
        formattedCreatedAt() {
            return this.formatDate(this.jobDetails.data?.created_at);
        },
        formattedUpdatedAt() {
            return this.formatDate(this.jobDetails.data?.updated_at);
        },

        displayedJobDetails() {
            if (!this.jobDetails.data) return {};
            const displayedData = { ...this.jobDetails.data };
            if (displayedData.Status) {
                const foundStatus = this.statuses.find(s => s.value === displayedData.Status);
                displayedData.Status = foundStatus ? foundStatus.title : displayedData.Status;
            }
            if (displayedData.Abholtermin) {
                displayedData.Abholtermin = this.formatDate(displayedData.Abholtermin);
            }
            return displayedData;
        },
        formattedAbholterminForEdit: {
            get() {
                if (!this.editedJobData.Abholtermin) return '';
                try {
                    const date = new Date(this.editedJobData.Abholtermin);
                    return date.toISOString().slice(0, 16);
                } catch {
                    return '';
                }
            },
            set(newValue) {
                if (newValue) {
                    try {
                        const date = new Date(newValue);
                        this.editedJobData.Abholtermin = date.toISOString();
                    } catch {
                        this.editedJobData.Abholtermin = null;
                    }
                } else {
                    this.editedJobData.Abholtermin = null;
                }
            }
        }
    },
    async mounted() {
        try {
            await this.getJob();
            await this.fetchCustomers();
            await this.fetchCars();
            await this.fetchServices();
        } catch (error) {
            this.error = error.message;
            this.showSnackbar(error.message, 'error');
        } finally {
            this.loading = false;
        }
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return 'Unbekannt';

            try {
                return new Date(dateString).toLocaleDateString('de-DE', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch {
                return 'Ungültiges Datum';
            }
        },

        async getJob() {
            try {
                if (!this.$route?.params?.id) {
                    throw new Error("Keine Job-ID angegeben");
                }

                const { data } = await axios.get(
                    `/api/jobs/jobdetails/${this.$route.params.id}`
                );

                if (!data) {
                    throw new Error("Keine Daten vom Server erhalten");
                }
                this.jobDetails = data;
                this.editedJobData = { ...this.jobDetails.data };

                // Map status to its value for the dropdown
                if (this.jobDetails.data.Status) {
                    const foundStatus = this.statuses.find(s => s.title === this.jobDetails.data.Status);
                    this.editedJobData.Status = foundStatus ? foundStatus.value : this.jobDetails.data.Status;
                }

                console.log('JobDetails component received jobDetails:', this.jobDetails);
                console.log('JobDetails component received car data:', this.jobDetails.data.car);

                // Set customer for autocomplete
                if (this.jobDetails.data.customer) {
                    this.editedJobData.customer = {
                        id: this.jobDetails.data.customer.id,
                        full_name: `${this.jobDetails.data.customer.firstname} ${this.jobDetails.data.customer.lastname}`,
                        email: this.jobDetails.data.customer.email
                    };
                } else {
                    this.editedJobData.customer = null;
                }

                // Set car for autocomplete
                if (this.jobDetails.data.car) {
                    this.editedJobData.car = {
                        id: this.jobDetails.data.car.id,
                        Kennzeichen: this.jobDetails.data.car.Kennzeichen,
                        Automarke: this.jobDetails.data.car.Automarke
                    };
                } else {
                    this.editedJobData.car = null;
                }

                // Set services for autocomplete
                if (this.jobDetails.data.services) {
                    this.editedJobData.services = this.jobDetails.data.services.map(service => ({
                        id: service.id,
                        name: service.name
                    }));
                } else {
                    this.editedJobData.services = [];
                }

            } catch (error) {
                this.error = error.response?.data?.message || error.message;
                this.showSnackbar("Fehler beim Laden der Jobdetails: " + this.error, 'error');
            } finally {
                this.loading = false;
            }
        },

        switchEditMode() {
            this.editMode = !this.editMode;

            if (this.editMode) {
                this.editedJobData = { ...this.jobDetails.data };
                console.log('Entering edit mode. editedJobData:', this.editedJobData);
                // Map status to its value for the dropdown
                if (this.jobDetails.data.Status) {
                    const foundStatus = this.statuses.find(s => s.title === this.jobDetails.data.Status);
                    this.editedJobData.Status = foundStatus ? foundStatus.value : this.jobDetails.data.Status;
                }

                if (this.editedJobData.Abholtermin) {
                    this.editedJobData.Abholtermin = this.formatDateTimeForInput(this.editedJobData.Abholtermin);
                }
                // Ensure services are mapped correctly for the autocomplete
                if (this.jobDetails.data.services) {
                    this.editedJobData.services = this.jobDetails.data.services.map(service => ({
                        id: service.id,
                        name: service.name
                    }));
                } else {
                    this.editedJobData.services = [];
                }
            }
        },

        cancelEdit() {
            this.editMode = false;
            this.editedJobData = { ...this.jobDetails.data };
            // Reset services to original for display
            if (this.jobDetails.data.services) {
                this.editedJobData.services = this.jobDetails.data.services.map(service => ({
                    id: service.id,
                    name: service.name
                }));
            } else {
                this.editedJobData.services = [];
            }
            // Reset status to original value for display
            if (this.jobDetails.data.Status) {
                const foundStatus = this.statuses.find(s => s.title === this.jobDetails.data.Status);
                this.editedJobData.Status = foundStatus ? foundStatus.value : this.jobDetails.data.Status;
            }
        },

        async saveJobData() {
            this.saveLoading = true;
            this.error = null;

            try {
                const dataToSubmit = { ...this.editedJobData };

                // Handle customer_id
                dataToSubmit.customer_id = dataToSubmit.customer ? dataToSubmit.customer.id : null;
                delete dataToSubmit.customer;

                // Handle car_id
                dataToSubmit.car_id = dataToSubmit.car ? dataToSubmit.car.id : null;
                delete dataToSubmit.car;

                // Handle services (send only IDs)
                dataToSubmit.services = dataToSubmit.services ? dataToSubmit.services.map(s => s.id) : [];

                // Handle status casing for backend
                if (Object.prototype.hasOwnProperty.call(dataToSubmit, 'Status')) {
                    dataToSubmit.status = dataToSubmit.Status;
                    delete dataToSubmit.Status;
                }

                // Rename keys to match backend expectations and ensure they are always present
                dataToSubmit.title = dataToSubmit.Title || null;
                delete dataToSubmit.Title;

                dataToSubmit.description = dataToSubmit.Beschreibung || null;
                delete dataToSubmit.Beschreibung;

                dataToSubmit.scheduled_at = dataToSubmit.Abholtermin || null;
                delete dataToSubmit.Abholtermin;

                console.log('Submitting job data:', dataToSubmit);

                await axios.put(
                    `/api/jobs/jobdetails/${this.$route.params.id}`,
                    dataToSubmit
                );

                // Reload the job details
                await this.getJob();
                this.editMode = false;
                this.showSnackbar("Jobdaten erfolgreich gespeichert", 'success');
            } catch (error) {
                console.error('Error saving job data:', error);
                console.error('Error response:', error.response?.data);

                let errorMessage = "Fehler beim Speichern der Jobdaten";

                if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response?.data?.errors) {
                    const validationErrors = Object.values(error.response.data.errors).flat();
                    errorMessage = validationErrors.join(', ');
                }

                this.showSnackbar(errorMessage, 'error');
            } finally {
                this.saveLoading = false;
            }
        },

        showSnackbar(text, color = 'success') {
            this.snackbar = {
                show: true,
                text,
                color,
            };
        },

        getIconForField(key) {
            const iconMap = {
                id: "mdi-identifier",
                Title: "mdi-format-title",
                Beschreibung: "mdi-text-box-outline",
                Abholtermin: "mdi-calendar",
                Status: "mdi-check-circle-outline",
                Kennzeichen: "mdi-car-info",
                Automarke: "mdi-car-traction-control",
                Typ: "mdi-car-cog",
                Farbe: "mdi-palette",
                Sonstiges: "mdi-information-outline",

                // Services Icons
                services: "mdi-tools",

                // Meta Data Icons
                created_at: "mdi-calendar-plus",
                updated_at: "mdi-calendar-edit",

                // Default fallback
                default: "mdi-information-outline"
            };

            return iconMap[key] || iconMap.default;
        },
        formatDateTimeForInput(dateString) {
            if (!dateString) return '';

            try {
                const date = new Date(dateString);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');

                return `${year}-${month}-${day}T${hours}:${minutes}`;
            } catch {
                return '';
            }
        },

        async fetchCustomers(query = '') {
            this.customersLoading = true;
            try {
                const response = await axios.get(`/api/customers/search?query=${query}`);
                this.customers = response.data.data.map(customer => ({
                    id: customer.id,
                    firstname: customer.firstname,
                    lastname: customer.lastname,
                    full_name: `${customer.firstname} ${customer.lastname}`,
                    email: customer.email
                }));
            } catch (error) {
                console.error('Error fetching customers:', error.response || error);
                this.showSnackbar('Fehler beim Laden der Kunden', 'error');
            } finally {
                this.customersLoading = false;
            }
        },

        searchCustomers(query) {
            if (this.customerSearchTimeout) {
                clearTimeout(this.customerSearchTimeout);
            }
            this.customerSearchTimeout = setTimeout(() => {
                this.fetchCustomers(query);
            }, 300);
        },

        async fetchCars(query = '') {
            this.carsLoading = true;
            try {
                const response = await axios.get(`/api/cars/search?query=${query}`);
                this.cars = response.data.data.map(car => ({
                    id: car.id,
                    Kennzeichen: car.Kennzeichen,
                    Automarke: car.Automarke
                }));
            } catch (error) {
                console.error('Error fetching cars:', error.response || error);
                this.showSnackbar('Fehler beim Laden der Fahrzeuge', 'error');
            } finally {
                this.carsLoading = false;
            }
        },

        searchCars(query) {
            if (this.carSearchTimeout) {
                clearTimeout(this.carSearchTimeout);
            }
            this.carSearchTimeout = setTimeout(() => {
                this.fetchCars(query);
            }, 300);
        },

        async fetchServices() {
            this.servicesLoading = true;
            try {
                const response = await axios.get(`/api/services`);
                this.services = response.data.data.map(service => ({
                    id: service.id,
                    name: service.name
                }));
            } catch (error) {
                console.error('Error fetching services:', error.response || error);
                this.showSnackbar('Fehler beim Laden der Dienstleistungen', 'error');
            } finally {
                this.servicesLoading = false;
            }
        },
    },
};
</script>

<style scoped>
.job-information-fields {
    width: 102%;
}

.card-container {
    width: 100%;
    height: calc(100vh - 40px);
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
}

.card {
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    transition: all 0.3s ease;
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}

@media (max-width: 575.98px) {
    .card-container {
        padding: 10px;
        height: calc(100vh - 20px);
    }

    .card {
        font-size: 14px;
    }
}

@media (min-width: 576px) and (max-width: 767.98px) {
    .card-container {
        padding: 15px;
        height: calc(100vh - 30px);
    }
}

@media (min-width: 768px) and (max-width: 991.98px) {
    .card-container {
        max-width: calc(100% - 80px);
    }
}

@media (min-width: 992px) and (max-width: 1199.98px) {
    .card-container {
        max-width: calc(100% - 250px);
    }
}

@media (min-width: 1200px) {
    .card-container {
        max-width: calc(100% - 280px);
    }
}

.v-card-text {
    flex: 1;
    overflow-y: auto;
}

@media (max-width: 767.98px) {
    .v-card-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .v-card-actions button {
        margin-bottom: 8px;
        width: 100%;
    }

    .v-spacer {
        display: none;
    }
}
</style>
