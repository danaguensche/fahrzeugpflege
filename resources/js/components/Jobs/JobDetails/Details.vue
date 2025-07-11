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
                        <InformationHeader :title="'Jobinformationen'" :editMode="editMode">
                        </InformationHeader>

                        <!-- Ansichtsmodus -->
                        <InfoList v-if="!editMode" :details="jobDetails" :labels="labels" :infoKeys="jobInfoKeys">
                        </InfoList>

                        <!-- Bearbeitungsmodus -->
                        <InfoListEditMode v-else :personalInfoKeys="jobInfoKeys" :labels="labels"
                            :editedData="editedJobData">
                        </InfoListEditMode>
                    </v-sheet>

                    <!-- Customer information -->
                    <v-sheet>
                        <DefaultHeader :title="'Kundeninformation'"></DefaultHeader>
                        <CustomerInfoList v-if="!editMode" :customer="jobDetails.data.customer" :customerId="jobDetails.data.customer_id"
                            :labels="labels">
                        </CustomerInfoList>

                        <v-autocomplete
                            v-else
                            v-model="editedJobData.customer"
                            :items="customers"
                            item-title="full_name"
                            item-value="id"
                            label="Kunde"
                            placeholder="Kunde auswählen oder suchen"
                            prepend-inner-icon="mdi-account"
                            variant="outlined"
                            density="comfortable"
                            hide-details="auto"
                            clearable
                            :loading="customersLoading"
                            :search-input.sync="customerSearch"
                            @update:search-input="searchCustomers"
                            return-object
                        >
                            <template v-slot:item="{ props, item }">
                                <v-list-item
                                    v-bind="props"
                                    :title="`${item.raw.firstname} ${item.raw.lastname}`"
                                    :subtitle="item.raw.email"
                                ></v-list-item>
                            </template>
                            <template v-slot:selection="{ item }">
                                {{ item.raw.email }}
                            </template>
                        </v-autocomplete>
                    </v-sheet>

                    <!-- Car information -->
                    <v-sheet>
                        <DefaultHeader :title="'Fahrzeuginformationen'"></DefaultHeader>
                        <InfoList v-if="!editMode" :details="jobDetails.data.car" :labels="labels" :infoKeys="carInfoKeys" :getIconForField="getIconForField">
                        </InfoList>

                        <v-autocomplete
                            v-else
                            v-model="editedJobData.car"
                            :items="cars"
                            item-title="Kennzeichen"
                            item-value="id"
                            label="Fahrzeug"
                            placeholder="Fahrzeug auswählen oder suchen"
                            prepend-inner-icon="mdi-car"
                            variant="outlined"
                            density="comfortable"
                            hide-details="auto"
                            clearable
                            :loading="carsLoading"
                            :search-input.sync="carSearch"
                            @update:search-input="searchCars"
                            return-object
                        >
                            <template v-slot:item="{ props, item }">
                                <v-list-item
                                    v-bind="props"
                                    :title="item.raw.Kennzeichen"
                                    :subtitle="item.raw.Automarke"
                                ></v-list-item>
                            </template>
                            <template v-slot:selection="{ item }">
                                {{ item.raw.Kennzeichen }}
                            </template>
                        </v-autocomplete>
                    </v-sheet>

                    <!-- Services information -->
                    <v-sheet>
                        <DefaultHeader :title="'Dienstleistungen'"></DefaultHeader>
                        <v-list v-if="!editMode" dense>
                            <v-list-item v-for="service in jobDetails.data.services" :key="service.id">
                                <v-list-item-title>{{ service.name }}</v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="jobDetails.data.services.length === 0">
                                <v-list-item-title>Keine Dienstleistungen zugewiesen</v-list-item-title>
                            </v-list-item>
                        </v-list>

                        <v-autocomplete
                            v-else
                            v-model="editedJobData.services"
                            :items="services"
                            item-title="name"
                            item-value="id"
                            label="Dienstleistungen"
                            placeholder="Dienstleistungen auswählen"
                            prepend-inner-icon="mdi-briefcase"
                            variant="outlined"
                            density="comfortable"
                            hide-details="auto"
                            multiple
                            chips
                            clearable
                            :loading="servicesLoading"
                            return-object
                        >
                            <template v-slot:chip="{ props, item }">
                                <v-chip
                                    v-bind="props"
                                    :text="item.raw.name"
                                ></v-chip>
                            </template>
                            <template v-slot:item="{ props, item }">
                                <v-list-item
                                    v-bind="props"
                                    :title="item.raw.name"
                                ></v-list-item>
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
import CarInfoList from "../../Details/CarInfoList.vue";

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
