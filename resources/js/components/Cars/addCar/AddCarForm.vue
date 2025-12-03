<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="700px">
        <v-card class="pa-2">
            <v-card-title class="headline pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-car</v-icon>
                Neues Fahrzeug hinzufügen
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-6">
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>
                        <v-col cols="12" sm="6">
                            <v-text-field v-model="car.Kennzeichen" label="Kennzeichen *"
                                :rules="[v => !!v || 'Kennzeichen ist erforderlich']" required variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-car-info" class="mb-3" :maxlength="10"
                                :counter="10"></v-text-field>
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

        
    </v-dialog>
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" location="bottom" :timeout="5000">
        {{ snackbar.text }}
        <template v-slot:actions>
            <v-btn color="white" variant="text" @click="snackbar.show = false">
                Schließen
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
import axios from 'axios';
import SnackBar from '../../Details/SnackBar.vue';

export default {
    name: 'AddCarForm',
    components: {
        SnackBar,
    },
    props: {
        modelValue: Boolean,
        addCustomerField: {
            type: Boolean,
            default: true,
        },
        addCarGroupField: {
            type: Boolean,
            default: true,
        },
    },
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
            },
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
                        service_ids: this.car.services ? this.car.services.map(s => s.id) : [],
                    };

                    await axios.post('/api/cars', carData);
                    this.$emit('car-added');
                    this.showSnackbar('Fahrzeug erfolgreich hinzugefügt', 'success');
                    this.closeDialog();
                } catch (error) {
                    console.error('Error saving car:', error);

                    // Handle validation errors (422 status)
                    if (error.response?.status === 422) {
                        const errors = error.response.data.errors;

                        // Spezifische Prüfung für Kennzeichen-Fehler
                        if (errors?.Kennzeichen) {
                            this.showSnackbar(errors.Kennzeichen[0], 'error');
                        } else {
                            // Zeige ersten Validierungsfehler
                            const firstError = Object.values(errors)[0][0];
                            this.showSnackbar(firstError, 'error');
                        }
                    } else {
                        this.showSnackbar(
                            error.response?.data?.message || 'Fehler beim Speichern des Fahrzeuges',
                            'error'
                        );
                    }
                } finally {
                    this.carsLoading = false;
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

        async fetchCarGroups(query = '') {
            this.carGroupsLoading = true;
            try {
                const response = await axios.get(`/api/cargroups/search?query=${query}`);
                console.log(response.data);
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



        fetchInitialData() {
            this.fetchCustomers();
            this.fetchCarGroups();
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