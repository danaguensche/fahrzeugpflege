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
                            <v-text-field
                                v-model="car.Kennzeichen"
                                label="Kennzeichen"
                                :rules="[v => !!v || 'Kennzeichen ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-car-info"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="car.Fahrzeugklasse"
                                label="Fahrzeugklasse"
                                :rules="[v => !!v || 'Fahrzeugklasse ist erforderlich']"
                                required
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-car-hatchback"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="car.Automarke"
                                label="Automarke"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-car"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="car.Typ"
                                label="Typ"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-car-info"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                            <v-text-field
                                v-model="car.Farbe"
                                label="Farbe"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-palette"
                                class="mb-3"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6" v-if="addCustomerField">
                            <v-autocomplete
                                v-model="car.customer"
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
                                    {{ item.raw.firstname }} {{ item.raw.lastname }}
                                </template>
                            </v-autocomplete>
                        </v-col>

                        <v-col cols="12">
                            <v-textarea
                                v-model="car.Sonstiges"
                                label="Sonstiges"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-note-text"
                                rows="3"
                                class="mb-3"
                            ></v-textarea>
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
                    @click="saveCar"
                    :loading="carsLoading"
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
                        ...this.car,
                        customer_id: this.car.customer ? this.car.customer.id : null,
                        service_ids: this.car.services ? this.car.services.map(s => s.id) : [],
                    };
                    delete carData.customer;

                    await axios.post('/api/cars', carData);
                    this.$emit('car-added');
                    this.showSnackbar('Fahrzeug erfolgreich hinzugefügt', 'success');
                    this.closeDialog();
                } catch (error) {
                    console.error('Error saving car:', error);
                    this.showSnackbar(error.response?.data?.message || 'Fehler beim Speichern des Fahrzeuges', 'error');
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
        
        searchCustomers(query) {
            if (this.customerSearchTimeout) {
                clearTimeout(this.customerSearchTimeout);
            }
            this.customerSearchTimeout = setTimeout(() => {
                this.fetchCustomers(query);
            }, 300);
        },

        fetchInitialData() {
            this.fetchCustomers();
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