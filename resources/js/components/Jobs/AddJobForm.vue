<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="600px">
        <v-card>
            <v-card-title class="headline">Neue Aufgabe hinzufügen</v-card-title>
            <v-card-text>
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-text-field
                        v-model="job.title"
                        label="Titel"
                        :rules="[v => !!v || 'Titel ist erforderlich']"
                        required
                    ></v-text-field>

                    <v-textarea
                        v-model="job.description"
                        label="Beschreibung"
                    ></v-textarea>

                    <v-autocomplete
                        v-model="job.car_id"
                        :items="cars"
                        item-title="data"
                        item-value="id"
                        label="Fahrzeug auswählen"
                        :rules="[v => !!v || 'Fahrzeug ist erforderlich']"
                        required
                        clearable
                        chips
                        small-chips
                        @update:search="searchCars"
                    ></v-autocomplete>

                    <v-autocomplete
                        v-model="job.customer_id"
                        :items="customers"
                        item-title="full_name"
                        item-value="id"
                        label="Kunde auswählen"
                        :rules="[v => !!v || 'Kunde ist erforderlich']"
                        required
                        clearable
                        chips
                        small-chips
                        @update:search="searchCustomers"
                    ></v-autocomplete>

                    <v-select
                        v-model="job.service_id"
                        :items="services"
                        item-title="title"
                        item-value="id"
                        label="Service auswählen"
                        :rules="[v => !!v || 'Service ist erforderlich']"
                        required
                        multiple
                        chips
                    ></v-select>
                    <v-select
                        v-model="job.status"
                        :items="jobStatuses"
                        label="Status"
                        :rules="[v => !!v || 'Status ist erforderlich']"
                        required
                    ></v-select>
                    <v-text-field
                        v-model="job.scheduled_at"
                        label="Abholtermin"
                        type="datetime-local"
                    ></v-text-field>

                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeDialog">Abbrechen</v-btn>
                <v-btn color="blue darken-1" text @click="saveJob">Speichern</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        modelValue: Boolean,
    },
    data() {
        return {
            valid: true,
            job: {
                title: '',
                description: '',
                car_id: null,
                customer_id: null,
                service_id: [],
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
            carSearchTimeout: null,
            customerSearchTimeout: null,
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
                this.fetchServices();
                // Optionally fetch initial cars/users if dropdowns are not empty
            }
        },
    },
    methods: {
        closeDialog() {
            this.$emit('update:modelValue', false);
            this.resetForm();
        },
        async saveJob() {
            if (this.$refs.form.validate()) {
                try {
                    await axios.post('/api/jobs', this.job);
                    this.$emit('job-added');
                    this.closeDialog();
                } catch (error) {
                    console.error('Error saving job:', error);
                    // Handle error (e.g., show a toast notification)
                }
            }
        },
        async fetchCars(query = '') {
            try {
                const response = await axios.get(`/api/cars/search?query=${query}`);
                this.cars = response.data.data.map(car => ({
                    id: car.id,
                    data: `${car.Automarke || ''} ${car.Typ || ''} ${car.Kennzeichen || ''}`.trim(),
                }));
            } catch (error) {
                console.error('Error fetching cars:', error);
            }
        },
        searchCars(newVal) {
            if (this.carSearchDebounce) {
                clearTimeout(this.carSearchDebounce);
            }
            this.carSearchDebounce = setTimeout(() => {
                this.fetchCars(newVal);
            }, 300);
        },
        async fetchCustomers(query = '') {
            try {
                const response = await axios.get(`/api/customers/search?query=${query}`);
                this.customers = response.data.data.map(customer => ({
                    id: customer.id,
                    full_name: `${customer.firstname || ''} ${customer.lastname || ''} ${customer.email || ''}`.trim(),
                }));
            } catch (error) {
                console.error('Error fetching customers:', error);
            }
        },
        async searchCustomers(newVal) {
            if (this.customerSearchDebounce) {
                clearTimeout(this.customerSearchDebounce);
            }
            this.customerSearchDebounce = setTimeout(() => {
                this.fetchCustomers(newVal);
            }, 300);
        },
        async fetchServices() {
            try {
                const response = await axios.get('/api/services');
                this.services = response.data.map(services => ({
                    id: services.id,
                    title: services.title,
                }));
            } catch (error) {
                console.error('Error fetching services:', error);
            }
        },
        resetForm() {
            this.$refs.form.reset();
            this.$refs.form.resetValidation();
            this.job = {
                title: '',
                description: '',
                car_id: null,
                customer_id: null,
                service_id: null,
                status: 'ausstehend',
                scheduled_at: null,
            };
        },
    },
};
</script>

<style scoped>
/* Add any specific styles for your form here */
</style>
