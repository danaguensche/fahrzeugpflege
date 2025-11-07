<!-- CarAddDialog.vue - Korrigierte Version -->
<template>
    <v-dialog v-model="dialog" max-width="700px">
        <v-card class="pa-2">
            <template v-if="step === 'checkExisting'">
                <v-card-title class="headline pa-6 pb-4">
                    <v-icon class="mr-3" color="primary">mdi-car-search</v-icon>
                    Fahrzeugauswahl
                </v-card-title>

                <v-divider></v-divider>

                <v-col cols="12">
                    <v-autocomplete 
                        v-model="selectedCar" 
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
                        class="mb-3"
                        @update:modelValue="handleCarSelection">
                        
                        <template v-slot:item="{ props, item }">
                            <v-list-item 
                                v-bind="props" 
                                :title="item.raw.Kennzeichen" 
                                :subtitle="item.raw.Automarke"
                                class="pa-3">
                            </v-list-item>
                        </template>
                        
                        <template v-slot:selection="{ item }">
                            {{ item.raw.Kennzeichen }}
                        </template>
                        
                        <template v-slot:no-data>
                            <v-list-item>
                                <v-list-item-title>
                                    {{ carsLoading ? 'Suche läuft...' : 'Keine Fahrzeuge gefunden' }}
                                </v-list-item-title>
                            </v-list-item>
                        </template>
                    </v-autocomplete>
                </v-col>

                <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
                    {{ snackbar.text }}
                </v-snackbar>

                <v-card-actions class="pa-4 grey lighten-4">
                    <v-btn color="green lighten-4" @click="openAddCarDialog" prepend-icon="mdi-plus" size="small"
                        class="mb-2">
                        Fahrzeug hinzufügen
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="selectExistingCar(selectedCar)" :disabled="!selectedCar" size="small" class="mb-2">
                        <v-icon left>mdi-check</v-icon>
                        hinzufügen
                    </v-btn>
                    <v-btn color="grey darken-1" text @click="closeDialog" size="small" class="mb-2">
                        <v-icon left>mdi-close</v-icon>
                        Abbrechen
                    </v-btn>
                </v-card-actions>

                <AddCarForm v-model="showAddCarDialog" @car-added="handleCarAdded" :addCustomerField="false" />

            </template>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios';
import AddCarForm from '../../Cars/addCar/AddCarForm.vue';

export default {
    name: 'CarAddDialog',
    components: {
        AddCarForm
    },
    props: {
        customerId: {
            type: [String, Number],
            default: null
        }
    },

    data() {
        return {
            dialog: false,
            showAddCarDialog: false,
            step: 'checkExisting',
            selectedCar: null,
            cars: [],
            carsLoading: false,
            carSearchTimeout: null,
            showDebug: false,
            
            snackbar: {
                show: false,
                text: '',
                color: 'success',
            },
            
            customer: {
                car: null
            }
        };
    },

    mounted() {
        this.fetchCars();
    },

    methods: {
        openAddCarDialog() {
            this.showAddCarDialog = true;
        },

        handleCarAdded(newCar) {
            this.showAddCarDialog = false;
            if (newCar) {
                this.cars.unshift({
                    id: newCar.id,
                    Kennzeichen: newCar.Kennzeichen,
                    Automarke: newCar.Automarke,
                });
                this.selectedCar = this.cars[0];
            }
        },

        open() {
            this.dialog = true;
            this.step = 'checkExisting';
            this.resetForm();
            this.fetchCars();
        },

        closeDialog() {
            this.dialog = false;
            this.resetForm();
        },

        resetForm() {
            this.selectedCar = null;
            this.customer.car = null;
            this.cars = [];
        },

        async fetchCars(query = '') {
            this.carsLoading = true;
            try {
                console.log('Fetching cars with query:', query);
                
                const response = await axios.get(`/api/cars/search?query=${encodeURIComponent(query)}`);
                
                console.log('API Response:', response.data);
                
                let carsData;
                if (response.data.data) {
                    carsData = response.data.data;
                } else if (Array.isArray(response.data)) {
                    carsData = response.data;
                } else {
                    carsData = [];
                }
                
                this.cars = carsData.map(car => ({
                    id: car.id,
                    Kennzeichen: car.Kennzeichen || car.kennzeichen || '',
                    Automarke: car.Automarke || car.automarke || '',
                }));
                
                console.log('Processed cars:', this.cars);
                
                if (this.cars.length === 0 && query) {
                    this.showSnackbar('Keine Fahrzeuge gefunden', 'info');
                }
                
            } catch (error) {
                console.error('Error fetching cars:', error);
                this.showSnackbar('Fehler beim Laden der Fahrzeuge: ' + error.message, 'error');
                this.cars = [];
            } finally {
                this.carsLoading = false;
            }
        },

        searchCars(query) {
            console.log('Search triggered with:', query); 
            
            if (this.carSearchTimeout) {
                clearTimeout(this.carSearchTimeout);
            }
            
            this.carSearchTimeout = setTimeout(() => {
                this.fetchCars(query);
            }, 300);
        },

        handleCarSelection(selectedCar) {
            if (selectedCar) {
                console.log('Car selected:', selectedCar);
                this.loadFullCarDetails(selectedCar.Kennzeichen);
            }
        },

        async loadFullCarDetails(kennzeichen) {
            try {
                const response = await axios.get(`/api/cars/${encodeURIComponent(kennzeichen)}`);
                this.customer.car = response.data.data || response.data;
                console.log('Full car details loaded:', this.customer.car);
            } catch (error) {
                console.error('Fehler beim Laden der Fahrzeugdetails:', error);
                this.showSnackbar('Fahrzeugdetails konnten nicht geladen werden', 'error');
            }
        },

        showSnackbar(text, color = 'info') {
            this.snackbar.text = text;
            this.snackbar.color = color;
            this.snackbar.show = true;
        },

        async selectExistingCar(car) {
            if (!car) {
                this.showSnackbar('Bitte wählen Sie ein Fahrzeug aus', 'warning');
                return;
            }

            try {
                const fullCarData = this.customer.car || await this.getFullCarData(car.Kennzeichen);
                
                await this.assignCarToCustomer(fullCarData || car);
                
                this.showSnackbar(`Fahrzeug ${car.Kennzeichen} wurde dem Kunden zugeordnet!`, 'success');
                
                setTimeout(() => {
                    this.$emit('car-selected', fullCarData || car);
                    this.closeDialog();
                }, 1500);
            } catch (error) {
                console.error('Fehler bei der Auswahl des Fahrzeugs:', error);
                this.showSnackbar('Fehler bei der Auswahl des Fahrzeugs', 'error');
            }
        },

        async getFullCarData(kennzeichen) {
            try {
                const response = await axios.get(`/api/cars/${encodeURIComponent(kennzeichen)}`);
                return response.data.data || response.data;
            } catch (error) {
                console.error('Fehler beim Laden der vollständigen Fahrzeugdaten:', error);
                return null;
            }
        },

        async assignCarToCustomer(car) {
            try {
                const customerId = this.customerId || this.$route.params.id;
                
                if (!customerId) {
                    throw new Error('Keine Kunden-ID verfügbar');
                }

                if (!car || !car.Kennzeichen) {
                    throw new Error('Ungültige Fahrzeugdaten');
                }

                const requestPayload = {
                    customer_id: customerId,
                    Kennzeichen: car.Kennzeichen,
                    Fahrzeugklasse: car.Fahrzeugklasse || null,
                    Automarke: car.Automarke || null,
                    Typ: car.Typ || null,
                    Farbe: car.Farbe || null,
                    Sonstiges: car.Sonstiges || null
                };

                console.log('Assigning car to customer:', requestPayload); // Debug
                
                await axios.put(`/api/cars/cardetails/${encodeURIComponent(car.Kennzeichen)}`, requestPayload);
                
                this.$emit('car-assigned', {
                    car: car,
                    customerId: customerId
                });
            } catch (error) {
                console.error('Error assigning car:', error);
                throw error;
            }
        }
    }
};
</script>

<style scoped>
.list-item-hover:hover {
    background-color: rgba(0, 0, 0, 0.04);
}

.v-card-title.headline {
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
}
</style>