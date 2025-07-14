<template>
    <v-dialog v-model="showDialogLocal" persistent max-width="700px">
        <v-card class="pa-2">
            <v-card-title class="headline pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-account-plus</v-icon>
                Neuen Kunden hinzufügen
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-6">
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="customer.company" label="Firma" variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-office-building"
                                class="mb-3"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="customer.firstname" label="Vorname"
                                :rules="[v => !!v || 'Vorname ist erforderlich']" required variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-account" class="mb-3"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="customer.lastname" label="Nachname"
                                :rules="[v => !!v || 'Nachname ist erforderlich']" required variant="outlined"
                                density="comfortable" class="mb-3"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="customer.email" label="E-Mail"
                                :rules="[v => !!v || 'E-Mail ist erforderlich']" required variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-email" type="email"
                                class="mb-3"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field v-model="customer.phonenumber" label="Telefonnummer" variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-phone" type="tel"
                                class="mb-3"></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-text-field v-model="customer.addressline" label="Straße und Hausnummer"
                                variant="outlined" density="comfortable" prepend-inner-icon="mdi-home"
                                class="mb-3"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="4">
                            <v-text-field v-model="customer.postalcode" label="Postleitzahl" variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-mailbox" class="mb-3"></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="8">
                            <v-text-field v-model="customer.city" label="Stadt" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-city" class="mb-3"></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-autocomplete v-model="customer.car" :items="cars" item-title="Kennzeichen"
                                item-value="id" label="Fahrzeug" placeholder="Fahrzeug auswählen oder suchen"
                                prepend-inner-icon="mdi-car" variant="outlined" density="comfortable" clearable
                                :loading="carsLoading" @update:search="searchCars" return-object class="mb-3"
                                @update:modelValue="(val) => val && loadFullCarDetails(val.Kennzeichen)">
                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props" :title="item.raw.Kennzeichen"
                                        :subtitle="item.raw.Automarke" class="pa-3"></v-list-item>
                                </template>
                                <template v-slot:selection="{ item }">
                                    {{ item.raw.Kennzeichen }}
                                </template>
                            </v-autocomplete>
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
                <v-btn variant="elevated" color="primary" @click="saveCustomer" :loading="customersLoading">
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
    name: 'AddCustomerForm',
    components: {
        SnackBar,
    },
    props: {
        modelValue: Boolean,
    },
    data() {
        return {
            valid: true,
            customer: {
                company: '',
                firstname: '',
                lastname: '',
                email: '',
                phonenumber: '',
                addressline: '',
                postalcode: '',
                city: '',
                car: null,
            },
            cars: [],
            carsLoading: false,
            customersLoading: false,
            carSearchTimeout: null,
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
        async saveCustomer() {
            const { valid } = await this.$refs.form.validate();
            if (!valid) return;

            this.customersLoading = true;

            try {
                // Nur Kundendaten speichern
                const customerData = {
                    company: this.customer.company || '',
                    firstname: this.customer.firstname,
                    lastname: this.customer.lastname,
                    email: this.customer.email || '',
                    phonenumber: this.customer.phonenumber || '',
                    adressline: this.customer.adressline || '',
                    postalcode: this.customer.postalcode || '',
                    city: this.customer.city || '',
                };

                // Kunde speichern
                const response = await axios.post('/api/customers', customerData);
                const newCustomer = response.data.data || response.data;

                // Jetzt Fahrzeug zuweisen, falls eins ausgewählt wurde
                if (this.customer.car) {
                    await this.assignCarToCustomer(this.customer.car, newCustomer.id);
                }

                this.$emit('customer-added');
                this.showSnackbar('Kunde erfolgreich hinzugefügt', 'success');
                this.closeDialog();
            } catch (error) {
                console.error('Fehler beim Speichern:', error.response?.data?.errors || error);
                this.showSnackbar(error.response?.data?.message || 'Fehler beim Speichern des Kunden', 'error');
            } finally {
                this.customersLoading = false;
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

        fetchInitialData() {
            this.fetchCars();
        },

        resetForm() {
            if (this.$refs.form) {
                this.$refs.form.reset();
                this.$refs.form.resetValidation();
            }
            this.customer = {
                company: '',
                firstname: '',
                lastname: '',
                email: '',
                phonenumber: '',
                addressline: '',
                postalcode: '',
                city: '',
                car: null,
            };
        },

        showSnackbar(text, color = 'success') {
            this.snackbar = {
                show: true,
                text,
                color,
            };
        },

        async assignCarToCustomer(car, customerId) {
            try {
                const requestPayload = {
                    customer_id: customerId,
                    Kennzeichen: car.Kennzeichen,
                    Fahrzeugklasse: car.Fahrzeugklasse || null,
                    Automarke: car.Automarke || null,
                    Typ: car.Typ || null,
                    Farbe: car.Farbe || null,
                    Sonstiges: car.Sonstiges || null,
                };

                await axios.put(`/api/cars/cardetails/${car.Kennzeichen}`, requestPayload);

                this.$emit('car-assigned', {
                    car,
                    customerId,
                });
            } catch (error) {
                console.error('Fehler beim Zuweisen des Fahrzeugs:', error.response?.data || error);
                throw error;
            }
        },

        async loadFullCarDetails(Kennzeichen) {
            try {
                const response = await axios.get(`/api/cars/${Kennzeichen}`);
                this.customer.car = response.data.data || response.data;
            } catch (error) {
                console.error('Fehler beim Laden der Fahrzeugdetails:', error.response?.data || error);
                this.showSnackbar('Fahrzeugdetails konnten nicht geladen werden', 'error');
            }
        }



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