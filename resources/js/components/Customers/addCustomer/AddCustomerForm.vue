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
                            <v-text-field
                                v-model="customer.company"
                                label="Firma"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-office-building"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>
                        
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.firstname"
                                label="Vorname"
                                :rules="[v => !!v || 'Vorname ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-account"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>
                        
                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.lastname"
                                label="Nachname"
                                :rules="[v => !!v || 'Nachname ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.email"
                                label="E-Mail"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-email"
                                type="email"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="customer.phonenumber"
                                label="Telefonnummer"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-phone"
                                type="tel"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-text-field
                                v-model="customer.adressline"
                                label="Straße und Hausnummer"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-home"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="4">
                            <v-text-field
                                v-model="customer.postalcode"
                                label="Postleitzahl"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-mailbox"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="8">
                            <v-text-field
                                v-model="customer.city"
                                label="Stadt"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-city"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-autocomplete
                                v-model="customer.car"
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
                    @click="saveCustomer"
                    :loading="customersLoading"
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
                adressline: '',
                postalcode: '',
                city: '',
            },
            cars: [],
            customersLoading: false,
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
        async saveCustomer() {
            const { valid } = await this.$refs.form.validate();
            if (valid) {
                this.customersLoading = true;
                try {
                    const customerData = {
                        ...this.customer,
                        car_id: this.customer.car ? this.customer.car.id : null,
                    };
                    delete customerData.car;

                    await axios.post('/api/customers', customerData);
                    this.$emit('customer-added');
                    this.showSnackbar('Kunde erfolgreich hinzugefügt', 'success');
                    this.closeDialog();
                } catch (error) {
                    console.error('Error saving customer:', error);
                    this.showSnackbar(error.response?.data?.message || 'Fehler beim Speichern des Kunden', 'error');
                } finally {
                    this.customersLoading = false;
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
                car: null,
                email: '',
                phonenumber: '',
                adressline: '',
                postalcode: '',
                city: '',
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