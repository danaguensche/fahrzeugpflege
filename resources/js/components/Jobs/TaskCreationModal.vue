<template>
    <v-dialog v-model="showModal" max-width="600px">
        <v-card>
            <v-card-title>
                <span class="headline">Neue Aufgabe hinzufügen</span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-autocomplete
                                label="Fahrzeug auswählen"
                                :items="cars"
                                item-title="full_name"
                                item-value="id"
                                v-model="newTask.car_id"
                                :search-input.sync="searchCarText"
                                @update:search-input="searchCars"
                                return-object
                                clearable
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-autocomplete
                                label="Kunden auswählen"
                                :items="customers"
                                item-title="full_name"
                                item-value="id"
                                v-model="newTask.customer_id"
                                :search-input.sync="searchCustomerText"
                                @update:search-input="searchCustomers"
                                return-object
                                clearable
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-select
                                label="Services auswählen"
                                :items="services"
                                item-title="name"
                                item-value="id"
                                v-model="newTask.service_ids"
                                multiple
                                chips
                                clearable
                            ></v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                label="Abholtermin"
                                v-model="newTask.pickup_date"
                                type="date"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field
                                label="Titel"
                                v-model="newTask.title"
                                required
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea
                                label="Beschreibung"
                                v-model="newTask.description"
                            ></v-textarea>
                        </v-col>
                        <v-col cols="12">
                            <v-select
                                label="Status"
                                :items="['pending', 'completed', 'cancelled']"
                                v-model="newTask.status"
                                required
                            ></v-select>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="closeModal">Abbrechen</v-btn>
                <v-btn color="blue darken-1" text @click="saveTask">Speichern</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        show: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            showModal: this.show,
            newTask: {
                car_id: null,
                customer_id: null,
                service_ids: [],
                pickup_date: null,
                title: '', // Initialize title
                description: '', // Initialize description
                status: 'pending', // Initialize status with a default
            },
            cars: [],
            customers: [],
            services: [],
            searchCarText: null,
            searchCustomerText: null,
            carSearchDebounce: null,
            customerSearchDebounce: null,
        };
    },
    watch: {
        show(val) {
            this.showModal = val;
            if (val) {
                this.fetchServices();
                // Initial fetch for cars and customers, or fetch when search input changes
                this.fetchCars();
                this.fetchCustomers();
            }
        },
        showModal(val) {
            if (!val) {
                this.$emit('close');
                this.resetForm();
            }
        }
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        resetForm() {
            this.newTask = {
                car_id: null,
                customer_id: null,
                service_ids: [],
                pickup_date: null,
                title: '',
                description: '',
                status: 'pending',
            };
            this.cars = [];
            this.customers = [];
            this.services = [];
            this.searchCarText = null;
            this.searchCustomerText = null;
        },
        async fetchCars(query = '') {
            try {
                const response = await axios.get(`/api/cars/search?query=${query}`);
                this.cars = response.data.data.map(car => ({
                    id: car.id,
                    Kennzeichen: car.Kennzeichen,
                    full_name: `${car.Kennzeichen} (${car.Automarke} ${car.Typ})`
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
                    full_name: `${customer.firstname} ${customer.lastname}`,
                }));
            } catch (error) {
                console.error('Error fetching customers:', error);
            }
        },
        searchCustomers(newVal) {
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
                this.services = response.data.data;
            } catch (error) {
                console.error('Error fetching services:', error);
            }
        },
        async saveTask() {
            try {
                // Prepare data for API call
                const payload = {
                    car_id: this.newTask.car_id ? this.newTask.car_id.id : null,
                    customer_id: this.newTask.customer_id ? this.newTask.customer_id.id : null,
                    service_ids: this.newTask.service_ids,
                    scheduled_at: this.newTask.pickup_date,
                    title: this.newTask.title, // Use value from input
                    description: this.newTask.description, // Use value from input
                    status: this.newTask.status, // Use value from input
                };
                const response = await axios.post('/api/jobs', payload);
                console.log('Task saved:', response.data);
                this.$emit('taskCreated');
                this.closeModal();
            } catch (error) {
                console.error('Error saving task:', error);
                // Handle error, e.g., show a toast message
            }
        }
    }
};
</script>

<style scoped>
/* Add any specific styles for the modal here if needed */
</style>
