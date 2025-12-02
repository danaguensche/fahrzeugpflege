<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="700px">
        <v-card class="pa-2">
            <v-card-title class="headline pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-car</v-icon>
                Fahrzeug bearbeiten
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-6">
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-text-field v-model="car.Kennzeichen" label="Kennzeichen *"
                                :rules="[v => !!v || 'Kennzeichen ist erforderlich']" required variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-car-info" class="mb-3" :maxlength="10"
                                :counter="10" disabled></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6" v-if="addCarGroupField">
                            <v-autocomplete v-model="car.Fahrzeugklasse" :items="carGroups" item-title="title"
                                item-value="title" label="Fahrzeugklasse *"
                                placeholder="Fahrzeugklasse auswählen oder suchen" prepend-inner-icon="mdi-car-multiple"
                                variant="outlined" density="comfortable" required clearable :loading="carGroupsLoading"
                                @update:search="searchCarGroups" class="mb-3">
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="car.Automarke" label="Automarke" variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-car" class="mb-3" :maxlength="24"
                                :counter="24"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="car.Typ" label="Typ" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-car-info" class="mb-3" :maxlength="24"
                                :counter="24"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="car.Farbe"
                                :rules="[v => !v || /^[A-Za-z]+$/.test(v) || 'Bitte geben Sie eine gültige Farbe ein']"
                                label="Farbe" variant="outlined" density="comfortable" prepend-inner-icon="mdi-palette"
                                class="mb-3" :maxlength="24" :counter="24"></v-text-field>
                        </v-col>

                        <!-- Kunde hinzufügen (mit Suche und Autovervollständigung) -->
                        <v-col cols="12" sm="6" v-if="addCustomerField">
                            <v-autocomplete v-model="car.customer" :items="customers" item-title="full_name"
                                item-value="id" label="Kunde" placeholder="Kunde auswählen oder suchen"
                                prepend-inner-icon="mdi-account" variant="outlined" density="comfortable" clearable
                                :loading="customersLoading" @update:search="searchCustomers" return-object class="mb-3">

                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="`${item.raw.firstname} ${item.raw.lastname}`"
                                        :subtitle="item.raw.email" class="pa-3"></v-list-item>
                                </template>
                                <template v-slot:selection="{ item }">
                                    {{ item.raw.firstname }} {{ item.raw.lastname }}
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12">
                            <v-textarea v-model="car.Sonstiges" label="Sonstiges" variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-note-text" rows="3" class="mb-3"
                                :maxlength="65000" :counter="65000">
                            </v-textarea>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>

            <v-divider></v-divider>

            <!-- Buttons -->
            <v-card-actions class="pa-6 pt-4">
                <v-spacer></v-spacer>
                <v-btn variant="outlined" color="grey" @click="closeDialog" class="mr-3">
                    <v-icon start>mdi-close</v-icon>
                    Abbrechen
                </v-btn>

                <v-btn variant="elevated" color="primary" @click="saveCar" :loading="carsLoading">
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
import SnackBar from '../../Details/SnackBar.vue';

export default {
    name: 'EditCarForm',
    components: {
        SnackBar,
    },
    props: {
        modelValue: Boolean,
        carData: {
            type: Object,
            default: null,
        },
        addCustomerField: {
            type: Boolean,
            default: true,
        },
        addCarGroupField: {
            type: Boolean,
            default: true,
        },
    },

    emits: ['update:modelValue', 'car-edited'],

    data() {
        return {
            valid: true,
            car: {
                Kennzeichen: '',
                Fahrzeugklasse: '',
                Automarke: '',
                Typ: '',
                Farbe: '',
                Sonstiges: '',
                customer: null,
            },
            originalKennzeichen: '',
            customers: [],
            carGroups: [],
            carGroupsLoading: false,
            carsSearchTimeout: null,
            customersLoading: false,
            carsLoading: false,
            customerSearchTimeout: null,
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
        modelValue: {
            async handler(val) {
                if (val && this.carData) {
                    //Daten für Dropdown
                    await this.fetchInitialData();
                    await this.getFullFormData();
                }
            },
            immediate: false,
        },
    },

    methods: {
        async getFullFormData() {
            if (!this.carData) return;

            let fullCarData = this.carData;

            try {
                const response = await axios.get(`/api/cars/${encodeURIComponent(this.carData.Kennzeichen)}`);
                fullCarData = response.data.data || response.data;
                console.log('Full car data:', fullCarData);
            } catch (error) {
                console.error('Error fetching full car data:', error);
            }

            this.car = {
                Kennzeichen: fullCarData.Kennzeichen || '',
                Fahrzeugklasse: fullCarData.Fahrzeugklasse || '',
                Automarke: fullCarData.Automarke || '',
                Typ: fullCarData.Typ || '',
                Farbe: fullCarData.Farbe || '',
                Sonstiges: fullCarData.Sonstiges || '',
                customer: null,
            };
            this.originalKennzeichen = fullCarData.Kennzeichen || '';

            await this.setCustomer(fullCarData);

            // Fahrzeugklasse in carGroups
            if (fullCarData.Fahrzeugklasse) {
                const existingGroup = this.carGroups.find(g => g.title === fullCarData.Fahrzeugklasse);
                if (!existingGroup) {
                    this.carGroups.push({
                        id: null,
                        title: fullCarData.Fahrzeugklasse,
                    });
                }
            }
        },

        async setCustomer(data) {
            let customerId = null;
            let customerObj = null;

            // Prüfe ob customer als Objekt oder nur als ID vorhanden ist
            if (data.customer && typeof data.customer === 'object') {
                customerObj = data.customer;
                customerId = customerObj.id;
            } else if (data.customer_id) {
                customerId = data.customer_id;
            }

            if (!customerId) return;

            // Prüfe ob Customer bereits in der Liste ist
            let existingCustomer = this.customers.find(c => c.id === customerId);

            if (!existingCustomer && customerObj) {
                existingCustomer = {
                    id: customerObj.id,
                    firstname: customerObj.firstname,
                    lastname: customerObj.lastname,
                    full_name: `${customerObj.firstname} ${customerObj.lastname}`,
                    email: customerObj.email,
                };
                this.customers.push(existingCustomer);
            } else if (!existingCustomer) {
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
                    return;
                }
            }

            this.car.customer = existingCustomer;
            console.log('Customer set:', this.car.customer);
        },

        closeDialog() {
            this.$emit('update:modelValue', false);
            this.resetForm();
        },

        async saveCar() {
            const { valid } = await this.$refs.form.validate();
            if (valid) {
                this.carsLoading = true;
                try {
                    const carData = {
                        Kennzeichen: this.car.Kennzeichen,
                        Fahrzeugklasse: this.car.Fahrzeugklasse || null,
                        Automarke: this.car.Automarke,
                        Typ: this.car.Typ,
                        Farbe: this.car.Farbe,
                        Sonstiges: this.car.Sonstiges,
                        customer_id: this.car.customer ? this.car.customer.id : null,
                    };

                    await axios.put(`/api/cars/${encodeURIComponent(this.originalKennzeichen)}`, carData);
                    this.$emit('car-edited');
                    this.showSnackbar('Fahrzeug erfolgreich aktualisiert', 'success');
                    this.closeDialog();
                } catch (error) {
                    console.error('Error updating car:', error);
                    this.showSnackbar(error.response?.data?.message || 'Fehler beim Aktualisieren des Fahrzeuges', 'error');
                } finally {
                    this.carsLoading = false;
                }
            }
        },

        async fetchCustomers(query = '') {
            this.customersLoading = true;
            try {
                const response = await axios.get(`/api/customers/search?query=${query}`);
                const fetchedCustomers = response.data.data.map(customer => ({
                    id: customer.id,
                    firstname: customer.firstname,
                    lastname: customer.lastname,
                    full_name: `${customer.firstname} ${customer.lastname}`,
                    email: customer.email,
                }));

                // Aktuellen Kunden beibehalten falls vorhanden
                if (this.car.customer && !fetchedCustomers.find(c => c.id === this.car.customer.id)) {
                    fetchedCustomers.unshift(this.car.customer);
                }

                this.customers = fetchedCustomers;
            } catch (error) {
                console.error('Error fetching customers:', error);
                this.showSnackbar('Fehler beim Laden der Kunden', 'error');
            } finally {
                this.customersLoading = false;
            }
        },

        async fetchCarGroups(query = '') {
            this.carGroupsLoading = true;
            try {
                const response = await axios.get(`/api/cargroups/search?query=${query}`);
                this.carGroups = response.data.data.map(group => ({
                    id: group.id,
                    title: group.title,
                }));
            } catch (error) {
                console.error('Error fetching car groups:', error);
                this.showSnackbar('Fehler beim Laden der Fahrzeugklassen', 'error');
            } finally {
                this.carGroupsLoading = false;
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

        searchCarGroups(query) {
            if (this.carsSearchTimeout) {
                clearTimeout(this.carsSearchTimeout);
            }
            this.carsSearchTimeout = setTimeout(() => {
                this.fetchCarGroups(query);
            }, 300);
        },

        async fetchInitialData() {
            await Promise.all([
                this.fetchCustomers(),
                this.fetchCarGroups(),
            ]);
        },

        resetForm() {
            if (this.$refs.form) {
                this.$refs.form.reset();
                this.$refs.form.resetValidation();
            }
            this.car = {
                Kennzeichen: '',
                Fahrzeugklasse: '',
                Automarke: '',
                customer: null,
                Typ: '',
                Farbe: '',
                Sonstiges: '',
            };
            this.originalKennzeichen = '';
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
