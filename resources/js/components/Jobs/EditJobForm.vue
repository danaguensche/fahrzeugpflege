<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="700px">
        <v-card class="pa-2">
            <v-card-title class="headline pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-briefcase-edit</v-icon>
                Auftrag bearbeiten
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-6">
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>
                        <!-- ID (nicht editierbar) -->
                        <v-col cols="12" sm="6">
                            <v-text-field v-model="job.id" label="ID" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-identifier" disabled class="mb-3">
                            </v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="job.title" label="Titel *"
                                :rules="[v => !!v || 'Titel ist erforderlich']" required variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-format-title" class="mb-3"
                                :maxlength="100" :counter="100">
                            </v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-textarea v-model="job.description" label="Beschreibung" variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-text" class="mb-3" :maxlength="65000"
                                :counter="65000">
                            </v-textarea>
                        </v-col>

                        <!-- Kunde -->
                        <v-col cols="12" sm="6">
                            <v-autocomplete v-model="job.customer" :items="customers" item-title="full_name"
                                item-value="id" label="Kunde *" placeholder="Kunde auswählen"
                                prepend-inner-icon="mdi-account" variant="outlined" density="comfortable" clearable
                                :loading="customersLoading" return-object
                                :rules="[v => !!v || 'Kunde ist erforderlich']" required class="mb-3"
                                @update:model-value="onCustomerChange">

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="`${item.raw.firstname} ${item.raw.lastname}`"
                                        :subtitle="item.raw.email" class="pa-3">
                                    </v-list-item>
                                </template>
                                <template v-slot:selection="{ item }">
                                    {{ item.raw.firstname }} {{ item.raw.lastname }}
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <!-- Fahrzeug -->
                        <v-col cols="12" sm="6">
                            <v-autocomplete v-model="job.car" :items="availableCars" item-title="Kennzeichen"
                                item-value="id" label="Fahrzeug *"
                                :placeholder="job.customer ? 'Fahrzeug auswählen' : 'Zuerst Kunde auswählen'"
                                prepend-inner-icon="mdi-car" variant="outlined" density="comfortable" clearable
                                :loading="carsLoading" return-object :disabled="!job.customer"
                                :rules="[v => !!v || 'Fahrzeug ist erforderlich']" required class="mb-3">

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="item.raw.Kennzeichen"
                                        :subtitle="item.raw.Automarke" class="pa-3">
                                    </v-list-item>
                                </template>
                                <template v-slot:selection="{ item }">
                                    {{ item.raw.Kennzeichen }}
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <!-- Services -->
                        <v-col cols="12">
                            <v-autocomplete v-model="job.services" :items="services" item-title="name" item-value="id"
                                label="Dienstleistungen *" placeholder="Dienstleistungen auswählen"
                                prepend-inner-icon="mdi-briefcase" variant="outlined" density="comfortable" multiple
                                chips clearable :loading="servicesLoading" return-object
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

                        <!-- Status -->
                        <v-col cols="12" sm="6">
                            <v-select v-model="job.status" :items="jobStatuses" label="Status *"
                                :rules="[v => !!v || 'Status ist erforderlich']" required variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-information" class="mb-3">
                            </v-select>
                        </v-col>

                        <!-- Trainee (nur für Admin/Trainer) -->
                        <template v-if="!isTrainee">
                            <v-col cols="12" sm="6">
                                <v-autocomplete v-model="job.trainee" :items="trainees" item-title="full_name"
                                    item-value="id" label="Auszubildender" placeholder="Auszubildenden auswählen"
                                    prepend-inner-icon="mdi-account-school" variant="outlined" density="comfortable"
                                    clearable :loading="traineesLoading" return-object class="mb-3">

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

                        <!-- Reinigungsstart -->
                        <v-col cols="12" sm="6">
                            <v-row dense>
                                <v-col sm="8">
                                    <v-text-field v-model="cleaning_start_date" label="Startdatum Reinigung *"
                                        type="date" variant="outlined" density="comfortable" class="mb-3"
                                        :rules="[v => !!v || 'Startdatum ist erforderlich']" required>
                                    </v-text-field>
                                </v-col>
                                <v-col sm="4">
                                    <v-text-field v-model="cleaning_start_time" label="Uhrzeit *" type="time"
                                        variant="outlined" density="comfortable" class="mb-3"
                                        :rules="[v => !!v || 'Startzeit ist erforderlich']" required>
                                    </v-text-field>
                                </v-col>
                            </v-row>
                        </v-col>

                        <!-- Reinigungsende -->
                        <v-col cols="12" sm="6">
                            <v-row dense>
                                <v-col sm="8">
                                    <v-text-field v-model="cleaning_end_date" label="Enddatum Reinigung *" type="date"
                                        variant="outlined" density="comfortable" class="mb-3"
                                        :rules="[v => !!v || 'Enddatum ist erforderlich']" required>
                                    </v-text-field>
                                </v-col>
                                <v-col sm="4">
                                    <v-text-field v-model="cleaning_end_time" label="Uhrzeit *" type="time"
                                        variant="outlined" density="comfortable" class="mb-3"
                                        :rules="[v => !!v || 'Endzeit ist erforderlich']" required>
                                    </v-text-field>
                                </v-col>
                            </v-row>
                        </v-col>

                        <!-- Abholtermin -->
                        <v-col cols="12" sm="6">
                            <v-row dense>
                                <v-col sm="8">
                                    <v-text-field v-model="scheduled_at_date" label="Abholtermin *" type="date"
                                        variant="outlined" density="comfortable" class="mb-3"
                                        :rules="[v => !!v || 'Abholtermin ist erforderlich']" required>
                                    </v-text-field>
                                </v-col>
                                <v-col sm="4">
                                    <v-text-field v-model="scheduled_at_time" label="Uhrzeit *" type="time"
                                        variant="outlined" density="comfortable" class="mb-3"
                                        :rules="[v => !!v || 'Abholzeit ist erforderlich']" required>
                                    </v-text-field>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions class="pa-6 pt-4">
                <v-spacer></v-spacer>
                <v-btn variant="outlined" color="grey" @click="closeDialog" class="mr-3">
                    <v-icon start>mdi-close</v-icon>
                    Abbrechen
                </v-btn>
                <v-btn variant="elevated" color="primary" @click="saveJob" :loading="loading">
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
    name: 'EditJobForm',

    components: {
        SnackBar,
    },

    props: {
        modelValue: Boolean,
        jobData: {
            type: Object,
            default: null,
        },
    },

    emits: ['update:modelValue', 'job-edited'],

    data() {
        return {
            valid: true,
            loading: false,
            originalId: null,
            job: {
                id: null,
                title: '',
                description: '',
                car: null,
                customer: null,
                services: [],
                status: 'ausstehend',
                trainee: null,
            },
            // Datum&Zeit-Felder
            cleaning_start_date: null,
            cleaning_start_time: null,
            cleaning_end_date: null,
            cleaning_end_time: null,
            scheduled_at_date: null,
            scheduled_at_time: null,
            // Dropdown-Daten
            trainees: [],
            customers: [],
            availableCars: [],
            services: [],
            jobStatuses: [
                { title: 'Ausstehend', value: 'ausstehend' },
                { title: 'In Bearbeitung', value: 'in_bearbeitung' },
                { title: 'im Rückblick', value: 'im_rueckblick' },
                { title: 'Abgeschlossen', value: 'abgeschlossen' },
            ],
            // Loading-States
            carsLoading: false,
            customersLoading: false,
            servicesLoading: false,
            traineesLoading: false,
            // Snackbar
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
        };
    },

    computed: {
        ...mapState('auth', ['userRole']),

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
    },

    watch: {
        modelValue: {
            async handler(val) {
                if (val && this.jobData) {
                    // Dropdown
                    await this.fetchInitialData();
                    await this.getFullFormData();
                }
            },
            immediate: false,
        },
    },

    methods: {
        async fetchFullJobData(jobId) {
            try {
                const response = await axios.get(`/api/jobs/${jobId}`);
                return response.data.data || response.data;
            } catch (error) {
                console.error('Error fetching full job data:', error);
                return null;
            }
        },

        // Formulardaten
        async getFullFormData() {
            if (!this.jobData) return;

            this.originalId = this.jobData.id;

            const fullJobData = await this.fetchFullJobData(this.jobData.id);
            const data = fullJobData || this.jobData;

            console.log('Full job data:', data);

            // Basis-Daten setzen
            this.job.id = data.id;
            this.job.title = data.title || '';
            this.job.description = data.description || '';
            this.job.status = data.status || 'ausstehend';

            this.parseDateTimeFields(data);

            // Customer setzen
            await this.setCustomer(data);

            await this.setCar(data);

            this.setServices(data);

            await this.setTrainee(data);
        },

        parseDateTimeFields(data) {
            // cleaning_start
            if (data.cleaning_start) {
                try {
                    const cleaningStart = new Date(data.cleaning_start);
                    if (!isNaN(cleaningStart.getTime())) {
                        this.cleaning_start_date = cleaningStart.toISOString().split('T')[0];
                        this.cleaning_start_time = cleaningStart.toTimeString().slice(0, 5);
                    }
                } catch (e) {
                    console.error('Error parsing cleaning_start:', e);
                }
            }

            // cleaning_end
            if (data.cleaning_end) {
                try {
                    const cleaningEnd = new Date(data.cleaning_end);
                    if (!isNaN(cleaningEnd.getTime())) {
                        this.cleaning_end_date = cleaningEnd.toISOString().split('T')[0];
                        this.cleaning_end_time = cleaningEnd.toTimeString().slice(0, 5);
                    }
                } catch (e) {
                    console.error('Error parsing cleaning_end:', e);
                }
            }

            // scheduled_at
            if (data.scheduled_at) {
                try {
                    const scheduledAt = new Date(data.scheduled_at);
                    if (!isNaN(scheduledAt.getTime())) {
                        this.scheduled_at_date = scheduledAt.toISOString().split('T')[0];
                        this.scheduled_at_time = scheduledAt.toTimeString().slice(0, 5);
                    }
                } catch (e) {
                    console.error('Error parsing scheduled_at:', e);
                }
            }
        },

        async setCustomer(data) {
            // Prüfe ob customer als Objekt oder nur als ID vorhanden ist
            let customerId = null;
            let customerObj = null;

            if (data.customer && typeof data.customer === 'object') {
                // Customer ist bereits ein Objekt
                customerObj = data.customer;
                customerId = customerObj.id;
            } else if (data.customer_id) {
                // Nur ID vorhanden
                customerId = data.customer_id;
            }

            if (customerId) {
                // Prüfe ob Customer bereits in der Liste ist
                let existingCustomer = this.customers.find(c => c.id === customerId);

                if (!existingCustomer && customerObj) {
                    // Customer-Objekt zur Liste hinzufügen
                    existingCustomer = {
                        id: customerObj.id,
                        firstname: customerObj.firstname,
                        lastname: customerObj.lastname,
                        full_name: `${customerObj.firstname} ${customerObj.lastname}`,
                        email: customerObj.email,
                    };
                    this.customers.push(existingCustomer);
                } else if (!existingCustomer) {
                    // Customer vom Backend laden
                    try {
                        const response = await axios.get(`/api/customers/${customerId}`);
                        const customer = response.data.data || response.data;
                        existingCustomer = {
                            id: customer.id,
                            firstname: customer.firstname,
                            lastname: customer.lastname,
                            full_name: `${customer.firstname} ${customer.lastname}`,
                            email: customer.email,
                        };
                        this.customers.push(existingCustomer);
                    } catch (error) {
                        console.error('Error loading customer:', error);
                    }
                }

                if (existingCustomer) {
                    this.job.customer = existingCustomer;
                    // Fahrzeuge für diesen Kunden laden
                    await this.fetchCarsForCustomer(customerId);
                }
            }
        },

        async setCar(data) {
            let carId = null;
            let carObj = null;

            if (data.car && typeof data.car === 'object') {
                carObj = data.car;
                carId = carObj.id;
            } else if (data.car_id) {
                carId = data.car_id;
            }

            if (carId) {
                // Prüfe ob Car bereits in availableCars ist
                let existingCar = this.availableCars.find(c => c.id === carId);

                if (!existingCar && carObj) {
                    existingCar = {
                        id: carObj.id,
                        Kennzeichen: carObj.Kennzeichen || carObj.license_plate,
                        Automarke: carObj.Automarke || carObj.brand,
                        customer_id: carObj.customer_id,
                    };
                    this.availableCars.push(existingCar);
                } else if (!existingCar) {
                    // Car vom Backend laden
                    try {
                        const response = await axios.get(`/api/cars/${carId}`);
                        const car = response.data.data || response.data;
                        existingCar = {
                            id: car.id,
                            Kennzeichen: car.Kennzeichen,
                            Automarke: car.Automarke,
                            customer_id: car.customer_id,
                        };
                        this.availableCars.push(existingCar);
                    } catch (error) {
                        console.error('Error loading car:', error);
                    }
                }

                if (existingCar) {
                    this.job.car = existingCar;
                }
            }
        },

        setServices(data) {
            if (data.services && Array.isArray(data.services)) {
                this.job.services = data.services.map(s => {
                    // Prüfe ob Service bereits in der Liste ist
                    const existingService = this.services.find(srv => srv.id === s.id);
                    if (existingService) {
                        return existingService;
                    }
                    // Service zur Liste hinzufügen
                    const newService = {
                        id: s.id,
                        name: s.name || s.title,
                    };
                    this.services.push(newService);
                    return newService;
                });
            }
        },

        async setTrainee(data) {
            let traineeId = null;
            let traineeObj = null;

            if (data.trainee && typeof data.trainee === 'object') {
                traineeObj = data.trainee;
                traineeId = traineeObj.id;
            } else if (data.trainee_id) {
                traineeId = data.trainee_id;
            }

            if (traineeId && !this.isTrainee) {
                let existingTrainee = this.trainees.find(t => t.id === traineeId);

                if (!existingTrainee && traineeObj) {
                    existingTrainee = {
                        id: traineeObj.id,
                        firstname: traineeObj.firstname,
                        lastname: traineeObj.lastname,
                        full_name: `${traineeObj.firstname} ${traineeObj.lastname}`,
                        email: traineeObj.email,
                    };
                    this.trainees.push(existingTrainee);
                } else if (!existingTrainee) {
                    try {
                        const response = await axios.get(`/api/users/${traineeId}`);
                        const trainee = response.data.data || response.data;
                        existingTrainee = {
                            id: trainee.id,
                            firstname: trainee.firstname,
                            lastname: trainee.lastname,
                            full_name: `${trainee.firstname} ${trainee.lastname}`,
                            email: trainee.email,
                        };
                        this.trainees.push(existingTrainee);
                    } catch (error) {
                        console.error('Error loading trainee:', error);
                    }
                }

                if (existingTrainee) {
                    this.job.trainee = existingTrainee;
                }
            }
        },

        closeDialog() {
            this.$emit('update:modelValue', false);
            this.resetForm();
        },

        combineDateTime(date, time) {
            if (!date) return null;
            if (!time) return `${date}T00:00:00`;
            return `${date}T${time}:00`;
        },

        async saveJob() {
            const { valid } = await this.$refs.form.validate();
            if (!valid) return;

            this.loading = true;
            try {
                const jobData = {
                    title: this.job.title,
                    description: this.job.description,
                    status: this.job.status,
                    car_id: this.job.car ? this.job.car.id : null,
                    customer_id: this.job.customer ? this.job.customer.id : null,
                    service_ids: this.job.services ? this.job.services.map(s => s.id) : [],
                    trainee_id: this.job.trainee ? this.job.trainee.id : null,
                    cleaning_start: this.combineDateTime(this.cleaning_start_date, this.cleaning_start_time),
                    cleaning_end: this.combineDateTime(this.cleaning_end_date, this.cleaning_end_time),
                    scheduled_at: this.combineDateTime(this.scheduled_at_date, this.scheduled_at_time),
                };

                await axios.put(`/api/jobs/${this.originalId}`, jobData);
                this.$emit('job-edited');
                this.showSnackbar('Auftrag erfolgreich aktualisiert', 'success');
                this.closeDialog();
            } catch (error) {
                console.error('Error updating job:', error);
                this.showSnackbar(
                    error.response?.data?.message || 'Fehler beim Aktualisieren des Auftrags',
                    'error'
                );
            } finally {
                this.loading = false;
            }
        },

        async onCustomerChange(customer) {
            this.job.car = null;
            this.availableCars = [];
            if (customer) {
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
                    customer_id: car.customer_id,
                }));
            } catch (error) {
                console.error('Error fetching cars:', error);
            } finally {
                this.carsLoading = false;
            }
        },

        async fetchCustomers() {
            this.customersLoading = true;
            try {
                const response = await axios.get('/api/customers/search?query=');
                this.customers = response.data.data.map(customer => ({
                    id: customer.id,
                    firstname: customer.firstname,
                    lastname: customer.lastname,
                    full_name: `${customer.firstname} ${customer.lastname}`,
                    email: customer.email,
                }));
            } catch (error) {
                console.error('Error fetching customers:', error);
            } finally {
                this.customersLoading = false;
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
            } finally {
                this.servicesLoading = false;
            }
        },

        async fetchTrainees() {
            if (this.isTrainee) return;

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
            } finally {
                this.traineesLoading = false;
            }
        },

        async fetchInitialData() {
            await Promise.all([
                this.fetchCustomers(),
                this.fetchServices(),
                this.fetchTrainees(),
            ]);
        },

        resetForm() {
            if (this.$refs.form) {
                this.$refs.form.reset();
                this.$refs.form.resetValidation();
            }
            this.job = {
                id: null,
                title: '',
                description: '',
                car: null,
                customer: null,
                services: [],
                status: 'ausstehend',
                trainee: null,
            };
            this.cleaning_start_date = null;
            this.cleaning_start_time = null;
            this.cleaning_end_date = null;
            this.cleaning_end_time = null;
            this.scheduled_at_date = null;
            this.scheduled_at_time = null;
            this.availableCars = [];
            this.originalId = null;
        },

        showSnackbar(text, color = 'success') {
            this.snackbar = { show: true, text, color };
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
