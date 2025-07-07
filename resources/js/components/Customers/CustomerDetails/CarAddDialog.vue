<!-- CarAddDialog.vue -->
<template>
    <v-dialog v-model="dialog" max-width="700px">
        <v-card class="rounded-lg elevation-5">
            <template v-if="step === 'checkExisting'">
                <v-card-title class="headline primary white--text py-5">
                    <v-icon left large color="white">mdi-car-search</v-icon>
                    <span class="text-h5">Fahrzeugauswahl</span>
                </v-card-title>
                <v-card-text class="pa-7">
                    <v-row>
                        <v-col cols="12">
                            <v-form ref="existingCarForm" v-model="existingFormValid">
                                <v-text-field v-model="existingCarSearch"
                                    label="Fahrzeug-Kennzeichen oder -Marke eingeben"
                                    :rules="[v => !!v || 'Suchbegriff ist erforderlich']" @keyup.enter="searchCar"
                                    prepend-icon="mdi-magnify" clearable outlined class="rounded-lg"></v-text-field>
                            </v-form>
                        </v-col>
                    </v-row>

                    <!-- Suchergebnisse -->
                    <v-card v-if="searchResults.length > 0" class="mt-4" style="max-height: 500px; overflow-y: auto;">
                        <v-card-title>
                            <v-icon left class="mr-4" color="primary">mdi-format-list-bulleted</v-icon>
                            <span class="ga-10"></span>
                            Gefundene Fahrzeuge ({{ filteredSearchResults.length }})
                        </v-card-title>
                        <v-list two-line>
                            <v-list-item v-for="car in filteredSearchResults" :key="car.Kennzeichen" @click="selectExistingCar(car)" class="list-item-hover">
                            <v-avatar>
                                <v-icon color="primary">mdi-car</v-icon>
                            </v-avatar>
                            <v-list-item-title class="font-weight-bold">
                                {{ car.Kennzeichen }}
                            </v-list-item-title>
                            <v-list-item-subtitle>
                                {{ car.Automarke }} {{ car.Typ }} | {{ car.Farbe || 'Keine Farbangabe' }}
                            </v-list-item-subtitle>
                            <v-list-item-action style="position: absolute; top: 10px; right: 0;">
                                <v-btn icon variant="plain" @click="selectExistingCar(car)">
                                <v-icon color="primary">mdi-plus-circle</v-icon>
                                </v-btn>
                            </v-list-item-action>
                            </v-list-item>
                        </v-list>
                    </v-card>

                    <!-- Keine Suchergebnisse -->
                    <v-alert v-if="showNoResultsMessage" type="info" text class="mt-4 rounded-lg">
                        <div class="text-center">
                            <v-icon large color="info" class="mb-2">mdi-car-off</v-icon>
                            <p class="mb-2">Keine Fahrzeuge gefunden.</p>
                        </div>
                    </v-alert>

                    <!-- Erfolgsmeldung -->
                    <v-snackbar v-model="showSuccessMessage" color="success" timeout="3000" top>
                        <v-icon left>mdi-check-circle</v-icon>
                        {{ successMessage }}
                    </v-snackbar>
                </v-card-text>
                <v-card-actions class="pa-4 grey lighten-4">
                    <v-btn color="secondary" @click="step = 'createNew'">
                        <v-icon left>mdi-car-plus</v-icon>
                        Neues Fahrzeug
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="closeDialog">
                        <v-icon left>mdi-close</v-icon>
                        Abbrechen
                    </v-btn>
                    <v-btn color="primary" @click="searchCar" :disabled="!existingFormValid" :loading="isSearching">
                        <v-icon left>mdi-magnify</v-icon>
                        Suchen
                    </v-btn>
                </v-card-actions>
            </template>

            <!-- Neues Fahrzeug Schritt -->
            <template v-else-if="step === 'createNew'">
                <v-card-title class="headline primary white--text py-4">
                    <v-icon left large color="white">mdi-car-plus</v-icon>
                    <span class="text-h5">Neues Fahrzeug hinzufügen</span>
                </v-card-title>
                <v-card-text class="pa-5">
                    <v-form ref="carForm" v-model="valid" lazy-validation>
                        <v-container>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="newCar.Kennzeichen"
                                        :rules="[v => !!v || 'Kennzeichen ist erforderlich']" label="Kennzeichen"
                                        outlined required></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="newCar.Fahrzeugklasse"
                                        :rules="[v => !!v || 'Fahrzeugklasse ist erforderlich']" label="Fahrzeugklasse"
                                        outlined required></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="newCar.Automarke" label="Automarke" outlined></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="newCar.Typ" label="Typ" outlined></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="newCar.Farbe" label="Farbe" outlined></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="newCar.Sonstiges" label="Sonstiges" outlined></v-text-field>
                                </v-col>

                                <v-col v-if="customerId" cols="12">
                                    <v-alert type="info" text class="mb-0">
                                        Dieses Fahrzeug wird dem ausgewählten Kunden zugeordnet.
                                    </v-alert>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                </v-card-text>

                <v-card-actions class="pa-4 grey lighten-4">
                    <v-btn color="grey" text @click="step = 'checkExisting'">
                        <v-icon left>mdi-arrow-left</v-icon>
                        Zurück
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="closeDialog">
                        <v-icon left>mdi-close</v-icon>
                        Abbrechen
                    </v-btn>
                    <v-btn color="success" @click="saveCar" :disabled="!valid" :loading="isSaving">
                        <v-icon left>mdi-content-save</v-icon>
                        Speichern
                    </v-btn>
                </v-card-actions>
            </template>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios';

export default {
    name: 'CarAddDialog',
    props: {
        customerId: {
            type: [String, Number],
            default: null
        }
    },

    data() {
        return {
            dialog: false,
            step: 'checkExisting',
            valid: false,
            existingFormValid: false,
            existingCarSearch: '',
            searchResults: [],
            showNoResultsMessage: false,
            isSearching: false,
            isSaving: false,
            showSuccessMessage: false,
            successMessage: '',
            newCar: {
                Kennzeichen: '',
                Fahrzeugklasse: '',
                Automarke: '',
                Typ: '',
                Farbe: '',
                Sonstiges: '',
            },
        };
    },

    computed: {
        filteredSearchResults() {
            return this.searchResults.filter(car => !car.customer_id);
        }
    },

    methods: {
        open() {
            this.dialog = true;
            this.step = 'checkExisting';
            this.resetForm();
        },

        closeDialog() {
            this.dialog = false;
            this.resetForm();
        },

        resetForm() {
            this.newCar = {
                Kennzeichen: '',
                Fahrzeugklasse: '',
                Automarke: '',
                Typ: '',
                Farbe: '',
                Sonstiges: '',
            };
            this.existingCarSearch = '';
            this.searchResults = [];
            this.showNoResultsMessage = false;
            this.showSuccessMessage = false;

            this.$nextTick(() => {
                if (this.$refs.carForm) {
                    this.$refs.carForm.resetValidation();
                }
                if (this.$refs.existingCarForm) {
                    this.$refs.existingCarForm.resetValidation();
                }
            });
        },

        async searchCar() {
            if (this.$refs.existingCarForm && !this.$refs.existingCarForm.validate()) {
                return;
            }
            this.isSearching = true;

            try {
                console.log('Suche nach:', this.existingCarSearch);
                const response = await axios.get('/api/cars/search', {
                    params: { query: this.existingCarSearch }
                });

                if (response.data && Array.isArray(response.data.data)) {
                    this.searchResults = response.data.data;
                } else {
                    this.searchResults = [];
                }

                console.log('Verarbeitete Suchergebnisse:', this.searchResults);
                this.showNoResultsMessage = this.searchResults.length === 0;
            } catch (error) {
                console.error('Fehler bei der Fahrzeugsuche:', error);
                this.$emit('error', error.response?.data?.message || 'Fehler bei der Fahrzeugsuche');
                this.searchResults = [];
                this.showNoResultsMessage = true;
            } finally {
                this.isSearching = false;
            }
        },

        async selectExistingCar(car) {
            try {
                this.isSaving = true;
                await this.assignCarToCustomer(car);

                this.successMessage = `Fahrzeug ${car.Kennzeichen} wurde dem Kunden zugeordnet!`;
                this.showSuccessMessage = true;
                setTimeout(() => {
                    this.$emit('car-selected', car);
                    this.closeDialog();
                }, 1500);
            } catch (error) {
                console.error('Fehler bei der Auswahl des Fahrzeugs:', error);
                this.$emit('error', 'Fehler bei der Auswahl des Fahrzeugs');
                this.isSaving = false;
            }
        },

        async assignCarToCustomer(car) {
            try {
                const customerId = this.$route.params.id;
                const requestPayload = {
                    customer_id: customerId,
                    Kennzeichen: car.Kennzeichen,
                    Fahrzeugklasse: car.Fahrzeugklasse || null,
                    Automarke: car.Automarke || null,
                    Typ: car.Typ || null,
                    Farbe: car.Farbe || null,
                    Sonstiges: car.Sonstiges || null
                };
                await axios.put(`/api/cars/cardetails/${car.Kennzeichen}`, requestPayload);

                this.$emit('car-assigned', {
                    car: car,
                    customerId: customerId
                });
            } catch (error) {
                console.error('Error assigning car:', error);
                throw error;
            }
        },

        async saveCar() {
            if (this.$refs.carForm && !this.$refs.carForm.validate()) {
                return;
            }

            this.isSaving = true;

            try {
                console.log('Neues Fahrzeug:', this.newCar);
                const response = await axios.post('/api/cars', this.newCar);

                if (response.data) {
                    const newCar = response.data;
                    if (this.customerId) {
                        await this.assignCarToCustomer(newCar);

                        this.successMessage = `Neues Fahrzeug ${newCar.Kennzeichen} wurde erstellt und dem Kunden zugeordnet!`;
                        this.showSuccessMessage = true;
                        setTimeout(() => {
                            this.$emit('car-added', newCar);
                            this.closeDialog();
                        }, 1500);
                    } else {
                        this.$emit('car-added', newCar);
                        this.closeDialog();
                    }
                } else {
                    throw new Error('Keine Daten vom Server erhalten');
                }
            } catch (error) {
                console.error('Fehler beim Hinzufügen des Fahrzeuges:', error);
                const errorMessage = error.response?.data?.message || 'Fehler beim Hinzufügen des Fahrzeuges';
                this.$emit('error', errorMessage);
                this.isSaving = false;
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