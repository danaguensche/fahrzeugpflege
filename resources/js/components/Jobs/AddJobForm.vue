<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="700px">
        <v-card class="pa-2">
            <v-card-title class="headline pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-briefcase-plus</v-icon>
                Neue Aufgabe hinzufügen
            </v-card-title>
            
            <v-divider></v-divider>
            
            <v-card-text class="pa-6">
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                v-model="job.title"
                                label="Titel"
                                :rules="[v => !!v || 'Titel ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-format-title"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-textarea
                                v-model="job.description"
                                label="Beschreibung"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-text"
                                class="mb-3"
                            ></v-textarea>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-autocomplete
                                v-model="job.car"
                                :items="cars"
                                item-title="Kennzeichen"
                                item-value="id"
                                label="Fahrzeug"
                                placeholder="Fahrzeug auswählen oder suchen"
                                prepend-inner-icon="mdi-car"
                                variant="outlined"
                                density="comfortable"
                                clearable
                                :loading="carsLoading"
                                @update:search="searchCars"
                                return-object
                                :rules="[v => !!v || 'Fahrzeug ist erforderlich']"
                                required
                                class="mb-3"
                            >
                                <template v-slot:item="{ props, item }">
                                    <v-list-item
                                        v-bind="props"
                                        :title="item.raw.Kennzeichen"
                                        :subtitle="item.raw.Automarke"
                                        class="pa-3"
                                    ></v-list-item>
                                </template>
                                <template v-slot:selection="{ item }">
                                    {{ item.raw.Kennzeichen }}
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-autocomplete
                                v-model="job.customer"
                                :items="customers"
                                item-title="full_name"
                                item-value="id"
                                label="Kunde"
                                placeholder="Kunde auswählen oder suchen"
                                prepend-inner-icon="mdi-account"
                                variant="outlined"
                                density="comfortable"
                                clearable
                                :loading="customersLoading"
                                @update:search="searchCustomers"
                                return-object
                                :rules="[v => !!v || 'Kunde ist erforderlich']"
                                required
                                class="mb-3"
                            >
                                <template v-slot:item="{ props, item }">
                                    <v-list-item
                                        v-bind="props"
                                        :title="`${item.raw.firstname} ${item.raw.lastname}`"
                                        :subtitle="item.raw.email"
                                        class="pa-3"
                                    ></v-list-item>
                                </template>
                                <template v-slot:selection="{ item }">
                                    {{ item.raw.email }}
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12">
                            <v-autocomplete
                                v-model="job.services"
                                :items="services"
                                item-title="name"
                                item-value="id"
                                label="Dienstleistungen"
                                placeholder="Dienstleistungen auswählen"
                                prepend-inner-icon="mdi-briefcase"
                                variant="outlined"
                                density="comfortable"
                                multiple
                                chips
                                clearable
                                :loading="servicesLoading"
                                return-object
                                :rules="[v => v && v.length > 0 || 'Mindestens eine Dienstleistung ist erforderlich']"
                                required
                                class="mb-3"
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
                                        class="pa-3"
                                    ></v-list-item>
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-select
                                v-model="job.status"
                                :items="jobStatuses"
                                label="Status"
                                :rules="[v => !!v || 'Status ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-information"
                                class="mb-3"
                            ></v-select>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="job.scheduled_at"
                                label="Abholtermin"
                                type="datetime-local"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-calendar-clock"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            
            <v-divider></v-divider>
            
            <v-card-actions class="pa-6 pt-4">
                <v-spacer></v-spacer>
                <v-btn 
                    variant="outlined" 
                    color="grey" 
                    @click="closeDialog"
                    class="mr-3"
                >
                    <v-icon start>mdi-close</v-icon>
                    Abbrechen
                </v-btn>
                <v-btn 
                    variant="elevated" 
                    color="primary" 
                    @click="saveJob"
                    :loading="jobsLoading"
                >
                    <v-icon start>mdi-content-save</v-icon>
                    Speichern
                </v-btn>
            </v-card-actions>
        </v-card>
        
        <SnackBar 
            v-if="snackbar.show" 
            :text="snackbar.text" 
            :color="snackbar.color" 
            @close="snackbar.show = false"
        />
    </v-dialog>
</template>

<script>
import axios from 'axios';
import SnackBar from '../Details/SnackBar.vue';

export default {
    name: 'AddJobForm',
    components: {
        SnackBar,
    },
    props: {
        modelValue: Boolean,
    },
    data() {
        return {
            valid: true,
            job: {
                title: '',
                description: '',
                car: null,
                customer: null,
                services: [],
                status: 'ausstehend',
                scheduled_at: null,
            },
            cars: [],
            customers: [],
            services: [],
            jobStatuses: [
                { title: 'Ausstehend', value: 'ausstehend' },
                { title: 'In Bearbeitung', value: 'in_bearbeitung' },
                { title: 'Abgeschlossen', value: 'abgeschlossen' },
            ],
            carsLoading: false,
            carSearchTimeout: null,
            customersLoading: false,
            customerSearchTimeout: null,
            servicesLoading: false,
            jobsLoading: false,
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
        };
    },
    computed: {
        showDialogLocal: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            },
        },
    },
    watch: {
        modelValue(val) {
            if (val) {
                this.fetchInitialData();
            }
        },
    },
    methods: {
        closeDialog() {
            this.$emit('update:modelValue', false);
            this.resetForm();
        },
        async saveJob() {
            const { valid } = await this.$refs.form.validate();
            if (valid) {
                this.jobsLoading = true;
                try {
                    const jobData = {
                        ...this.job,
                        car_id: this.job.car ? this.job.car.id : null,
                        customer_id: this.job.customer ? this.job.customer.id : null,
                        service_ids: this.job.services ? this.job.services.map(s => s.id) : [],
                    };
                    delete jobData.car;
                    delete jobData.customer;
                    delete jobData.services;

                    await axios.post('/api/jobs', jobData);
                    this.$emit('job-added');
                    this.showSnackbar('Job erfolgreich hinzugefügt', 'success');
                    this.closeDialog();
                } catch (error) {
                    console.error('Error saving job:', error);
                    this.showSnackbar(error.response?.data?.message || 'Fehler beim Speichern des Jobs', 'error');
                } finally {
                    this.jobsLoading = false;
                }
            }
        },
        async fetchCars(query = '') {
            this.carsLoading = true;
            try {
                const response = await axios.get(`/api/cars/search?query=${query}`);
                this.cars = response.data.data.map(car => ({
                    id: car.id,
                    Kennzeichen: car.Kennzeichen,
                    Automarke: car.Automarke,
                }));
            } catch (error) {
                console.error('Error fetching cars:', error);
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
        async fetchCustomers(query = '') {
            this.customersLoading = true;
            try {
                const response = await axios.get(`/api/customers/search?query=${query}`);
                this.customers = response.data.data.map(customer => ({
                    id: customer.id,
                    firstname: customer.firstname,
                    lastname: customer.lastname,
                    full_name: `${customer.firstname} ${customer.lastname}`,
                    email: customer.email,
                }));
            } catch (error) {
                console.error('Error fetching customers:', error);
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
        async fetchServices() {
            this.servicesLoading = true;
            try {
                const response = await axios.get('/api/services');
                this.services = response.data.data.map(service => ({
                    id: service.id,
                    name: service.name,
                }));
            } catch (error) {
                console.error('Error fetching services:', error);
                this.showSnackbar('Fehler beim Laden der Dienstleistungen', 'error');
            } finally {
                this.servicesLoading = false;
            }
        },
        fetchInitialData() {
            this.fetchCars();
            this.fetchCustomers();
            this.fetchServices();
        },
        resetForm() {
            if (this.$refs.form) {
                this.$refs.form.reset();
                this.$refs.form.resetValidation();
            }
            this.job = {
                title: '',
                description: '',
                car: null,
                customer: null,
                services: [],
                status: 'ausstehend',
                scheduled_at: null,
            };
        },
        showSnackbar(text, color = 'success') {
            this.snackbar = {
                show: true,
                text,
                color,
            };
        },
    },
};
</script>

<style scoped>
.v-dialog {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

.v-card {
    border-radius: 12px;
}

.v-card-title {
    font-size: 1.25rem;
    font-weight: 600;
}
</style>