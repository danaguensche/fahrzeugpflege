<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="700px">
        <v-card class="pa-2">
            <v-card-title class="headline pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-briefcase-plus</v-icon>
                Neue Aufgabe hinzufügen
            </v-card-title>

            <v-divider></v-divider>

            <!-- Formular Felder mit Validierung -->

            <v-card-text class="pa-6">
                <v-form ref="form" v-model="valid" lazy-validation>

                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="job.title" label="Titel"
                                :rules="[v => !!v || 'Titel ist erforderlich']" required variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-format-title" class="mb-3">
                            </v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-textarea v-model="job.description" label="Beschreibung" variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-text" class="mb-3"></v-textarea>
                        </v-col>

                        <!-- Kunde zuerst auswählen -->
                        <v-col cols="12" sm="6">
                            <v-autocomplete v-model="job.customer" 
                                            :items="customers" 
                                            item-title="full_name"
                                            item-value="id" 
                                            label="Kunde" 
                                            placeholder="Kunde auswählen"
                                            prepend-inner-icon="mdi-account" 
                                            variant="outlined" 
                                            density="comfortable" 
                                            clearable
                                            :loading="customersLoading" 
                                            return-object
                                            :rules="[v => !!v || 'Kunde ist erforderlich']" 
                                            required class="mb-3"
                                            @update:model-value="onCustomerChange">

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" 
                                                :title="`${item.raw.firstname} ${item.raw.lastname}`"
                                                :subtitle="item.raw.email" 
                                                class="pa-3">
                                    </v-list-item>
                                </template>
                                <template v-slot:selection="{ item }">
                                    {{ item.raw.firstname }} {{ item.raw.lastname }}
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <!-- Fahrzeuge basierend auf Kundenauswahl (Es werden die Fahrzeuge angezeigt die zum Kunden gehören/noch nicht zugewiesen wurden)-->
                        <v-col cols="12" sm="6">
                            <v-autocomplete v-model="job.car" 
                                            :items="availableCars" 
                                            item-title="Kennzeichen"
                                            item-value="id" 
                                            label="Fahrzeug"
                                            :placeholder="job.customer ? 'Fahrzeug für Kunde auswählen' : 'Zuerst Kunde auswählen'"
                                            prepend-inner-icon="mdi-car" 
                                            variant="outlined" 
                                            density="comfortable" 
                                            clearable
                                            :loading="carsLoading" 
                                            return-object 
                                            :disabled="!job.customer"
                                            :rules="[v => !!v || 'Fahrzeug ist erforderlich']" 
                                            required 
                                            class="mb-3">

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="item.raw.Kennzeichen"
                                                                :subtitle="`${item.raw.Automarke} ${getCarOwnershipLabel(item.raw)}`"
                                                                class="pa-3">
                                    </v-list-item>
                                </template>

                                <template v-slot:selection="{ item }">
                                    {{ item.raw.Kennzeichen }}
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12">
                            <v-autocomplete v-model="job.services" 
                                            :items="services" 
                                            item-title="name" 
                                            item-value="id"
                                            label="Dienstleistungen" 
                                            placeholder="Dienstleistungen auswählen"
                                            prepend-inner-icon="mdi-briefcase" 
                                            variant="outlined" density="comfortable" 
                                            multiple chips clearable 
                                            :loading="servicesLoading" 
                                            return-object
                                            :rules="[v => v && v.length > 0 || 'Mindestens eine Dienstleistung ist erforderlich']"
                                            required class="mb-3">

                                <template v-slot:chip="{ props, item }">
                                    <v-chip v-bind="props" :text="item.raw.name"></v-chip>
                                </template>

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="item.raw.name" class="pa-3"></v-list-item>
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-select   v-model="job.status" 
                                        :items="jobStatuses" 
                                        label="Status"
                                        :rules="[v => !!v || 'Status ist erforderlich']" required variant="outlined"
                                        density="comfortable" prepend-inner-icon="mdi-information" class="mb-3">
                            </v-select>
                        </v-col>

                        <template v-if="!isTrainee">
                            <v-col cols="12" sm="6">
                                <v-autocomplete v-model="job.trainee" 
                                                :items="trainees" 
                                                item-title="full_name"
                                                item-value="id" 
                                                label="Auszubildender" 
                                                placeholder="Auszubildenden auswählen"
                                                prepend-inner-icon="mdi-account-school" 
                                                variant="outlined" 
                                                density="comfortable"
                                                clearable 
                                                :loading="traineesLoading" 
                                                return-object class="mb-3">
                                    <template v-slot:item="{ props, item }">
                                        <v-list-item v-bind="props"
                                            :title="`${item.raw.firstname} ${item.raw.lastname}`"
                                            :subtitle="item.raw.email" class="pa-3">
                                        </v-list-item>
                                    </template>
                                    <template v-slot:selection="{ item }">
                                        {{ item.raw.firstname }} {{ item.raw.lastname }}
                                    </template>
                                </v-autocomplete>
                            </v-col>
                        </template>

                        <v-col cols="12" sm="6">
                            <v-text-field   v-model="job.cleaning_start" 
                                            label="Startzeit Reinigung" 
                                            type="datetime-local"
                                            variant="outlined" 
                                            density="comfortable" 
                                            prepend-inner-icon="mdi-calendar-clock"
                                            class="mb-3">
                            </v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field   v-model="job.cleaning_end" 
                                            label="Endzeit Reinigung" 
                                            type="datetime-local"
                                            variant="outlined" 
                                            density="comfortable" 
                                            prepend-inner-icon="mdi-calendar-clock"
                                            class="mb-3">
                            </v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field   v-model="job.scheduled_at" 
                                            label="Abholtermin" 
                                            type="datetime-local"
                                            variant="outlined" 
                                            density="comfortable" 
                                            prepend-inner-icon="mdi-calendar-clock"
                                            class="mb-3">
                            </v-text-field>
                        </v-col>

                        <!-- Zuweisung eines Auszubildenden/Mitarbeiter nur für Admins und Mitarbeiter -->

                        

                        <!-- Fahrzeug zum Kunden zufügen falls das noch nicht gemacht wurde -->
                        <v-col cols="12" v-if="job.car && job.customer && !isCarOwnedByCustomer">
                            <v-alert type="info" variant="tonal" class="mb-3">
                                <v-icon start>mdi-information</v-icon>
                                Das ausgewählte Fahrzeug wird automatisch dem Kunden zugewiesen.
                            </v-alert>
                        </v-col>

                    </v-row>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>

            <!-- Aktions Buttons zum Abbrechen oder Speichern -->
            <v-card-actions class="pa-6 pt-4">
                <v-spacer></v-spacer>
                <v-btn variant="outlined" color="grey" @click="closeDialog" class="mr-3">
                    <v-icon start>mdi-close</v-icon>
                    Abbrechen
                </v-btn>
                <v-btn variant="elevated" color="primary" @click="saveJob" :loading="jobsLoading">
                    <v-icon start>mdi-content-save</v-icon>
                    Speichern
                </v-btn>
            </v-card-actions>
        </v-card>

        <SnackBar v-if="snackbar.show" :text="snackbar.text" :color="snackbar.color" @close="snackbar.show = false" />
    </v-dialog>
</template>

<script>
import axios from 'axios';
import SnackBar from '../Details/SnackBar.vue';
import { mapState } from 'vuex';

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
                cleaning_start: null,
                cleaning_end: null,
                scheduled_at: null,
                trainee: null,
            },
            trainees: [],
            customers: [],
            availableCars: [], // Fahrzeuge für den ausgewählten Kunden
            services: [],
            jobStatuses: [
                { title: 'Ausstehend', value: 'ausstehend' },
                { title: 'In Bearbeitung', value: 'in_bearbeitung' },
                { title: 'im Rückblick', value: 'im_rueckblick' },
                { title: 'Abgeschlossen', value: 'abgeschlossen' },
            ],
            carsLoading: false,
            customersLoading: false,
            servicesLoading: false,
            traineesLoading: false,
            jobsLoading: false,
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
        };
    },
    computed: {
        ...mapState('auth', ['userRole']),
        isAdmin() {
            return this.userRole === 'admin';
        },
        isTrainer() {
            return this.userRole === 'trainer';
        },
        isTrainee() {
            return this.userRole === 'trainee';
        },

        showDialogLocal: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            },
        },

        // Prüft ob das ausgewählte Auto bereits dem Kunden gehört
        isCarOwnedByCustomer() {
            if (!this.job.car || !this.job.customer) return false;
            return this.job.car.customer_id === this.job.customer.id;
        }
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
                        trainee_id: this.isTrainee ? this.$store.state.auth.userId : (this.job.trainee ? this.job.trainee.id : null),
                        assign_car_to_customer: true,
                        cleaning_start: this.job.cleaning_start || null,
                        cleaning_end: this.job.cleaning_end || null,
                        scheduled_at: this.job.scheduled_at || null,
                        
                    };
                    delete jobData.car;
                    delete jobData.customer;
                    delete jobData.services;
                    delete jobData.trainee;

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


        // Neue Methode: Wird aufgerufen wenn ein Kunde ausgewählt wird
        async onCustomerChange(customer) {
            this.job.car = null; // Auto-Auswahl zurücksetzen
            this.availableCars = [];

            if (customer) {
                console.log('Customer ID:', customer.id);
                await this.fetchCarsForCustomer(customer.id);
            }
        },

        async fetchCarsForCustomer(customerId) {
            this.carsLoading = true;
            try {
                const response = await axios.get(`/api/jobs/cars-for-customer/${customerId}`);
                this.availableCars = response.data.cars.map(car => ({
                    id: car.id,
                    Kennzeichen: car.Kennzeichen || car.license_plate,
                    Automarke: car.Automarke || car.brand,
                    customer_id: car.customer_id
                }));
            } catch (error) {
                console.error('Error fetching cars for customer:', error);
                this.showSnackbar('Fehler beim Laden der Fahrzeuge', 'error');
            } finally {
                this.carsLoading = false;
            }
        },


        // Hilfsmethode für Auto-Besitz Label
        getCarOwnershipLabel(car) {
            if (!car.customer_id) {
                return '(verfügbar)';
            } else if (this.job.customer && car.customer_id === this.job.customer.id) {
                return '(bereits zugewiesen)';
            }
            return '';
        },

        async fetchTrainees() {
            this.traineesLoading = true;
            try {
                const response = await axios.get('/api/users/trainees');
                this.trainees = response.data.data.map(trainee => ({
                    id: trainee.id,
                    firstname: trainee.firstname,
                    lastname: trainee.lastname,
                    full_name: `${trainee.firstname} ${trainee.lastname}`,
                    email: trainee.email,
                }));
            } catch (error) {
                console.error('Error fetching trainees:', error);
                this.showSnackbar('Fehler beim Laden der Auszubildenden', 'error');
            } finally {
                this.traineesLoading = false;
            }
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

        async fetchInitialData() {
            this.fetchCustomers(); // Jetzt verfügbare Kunden laden
            this.fetchServices();

            if (this.isAdmin || this.isTrainer) {
                await this.fetchTrainees();
            }
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
                cleaning_start: null,
                cleaning_end: null,
                scheduled_at: null,
                trainee: null,
            };
            this.availableCars = [];
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