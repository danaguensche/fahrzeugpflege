<template>
    <div class="component">
        <div class="header">
            <RefreshButton class="refresh-button" @refresh="loadItems(options)"></RefreshButton>
            <div class="spacer"></div>

            <div class="button-group">
                <ConfirmButton class="confirm-button" @click="confirmEditCar(editCarId)">Bestätigen</ConfirmButton>
                <CancelButton class="cancel-button">Abbrechen</CancelButton>
                <DeleteButton class="delete-button" :disabled="selectedCars.length === 0"
                    @click="confirmDeleteCars(selectedCars.kennzeichen)">
                    Löschen
                </DeleteButton>
            </div>

        </div>
        <div class="table-container">
            <div v-if="cars.length === 0"><v-progress-circular indeterminate></v-progress-circular></div>
            <div class="scrollable-table">
                <v-data-table-server :headers="headers" :items="cars" :options.sync="options"
                    :server-items-length="totalItems" :loading="loading" @update:options="loadItems">
                    <template v-slot:item="{ item }">
                        <tr>
                            <td class="checkbox fixed-width">
                                <v-checkbox v-model="selectedCars" :value="item.Kennzeichen"></v-checkbox>
                            </td>
                            <template v-for="field in fields" :key="field">
                                <td v-if="editCarId === item.Kennzeichen" class="edit-field fixed-width">
                                    <v-text-field v-model="editCar[field]"></v-text-field>
                                </td>
                                <td v-else class="fixed-width">
                                    <a v-if="field === 'Kennzeichen'"
                                        :href="'/fahrzeuge/fahrzeugdetails'">{{ item[field] }}</a>
                                    <span v-else>{{ item[field] }}</span>
                                </td>
                            </template>
                            <td class="table-icon fixed-width">
                                <v-btn icon class="delete-button" variant="plain"
                                    @click="confirmDeleteCars(item.Kennzeichen)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </td>
                            <td class="table-icon fixed-width">
                                <v-btn variant="plain" icon
                                    @click="editCarId === item.Kennzeichen ? saveCar(item.Kennzeichen) : editCarDetails(item)">
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table-server>
            </div>
        </div>
        <VuetifyAlert v-model="isAlertVisible" maxWidth="500" alertTypeClass="alertTypeConfirmation"
            :alertHeading="alertHeading" :alertParagraph="alertParagraph" :alertOkayButton="okayButton"
            alertCloseButton="Abbrechen" @confirmation="handleConfirmation">
        </VuetifyAlert>
    </div>
</template>

<script>
import RefreshButton from '../CommonSlots/RefreshButton.vue';
import axios from 'axios';
import VuetifyAlert from '../Alerts/VuetifyAlert.vue';
import ConfirmButton from '../CommonSlots/ConfirmButton.vue';
import CancelButton from '../CommonSlots/CancelButton.vue';
import DeleteButton from '../CommonSlots/DeleteButton.vue';

export default {
    name: "CarTable",
    components: {
        ConfirmButton,
        CancelButton,
        DeleteButton,
        RefreshButton,
        VuetifyAlert
    },
    data() {
        return {
            cars: [],
            selectedCars: [],
            carToDelete: null,
            isAlertVisible: false,
            headers: [
                { title: 'Auswählen', key: 'checkbox', sortable: false },
                { title: 'Kennzeichen', key: 'Kennzeichen' },
                { title: 'Fahrzeugklasse', key: 'Fahrzeugklasse' },
                { title: 'Automarke', key: 'Automarke' },
                { title: 'Typ', key: 'Typ' },
                { title: 'Farbe', key: 'Farbe' },
                { title: 'Löschen', key: 'actions', sortable: false },
                { title: 'Bearbeiten', key: 'actions', sortable: false }
            ],
            fields: ["Kennzeichen", "Fahrzeugklasse", "Automarke", "Typ", "Farbe"],
            editCarId: null,
            editCar: {},
            loading: false,
            totalItems: 0,
            alertHeading: '',
            alertParagraph: '',
            alertOkayButton: ''
        };
    },

    computed: {

        currentPage() {
            return this.options.page;
        },
        totalPages() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },

        options() {
            return {
                page: 1,
                itemsPerPage: 10,
                sortBy: [{ key: 'Kennzeichen' }],
                sortDesc: [false],
            };
        },

        isEditing() {
            return this.editCarId !== null;
        },
    },

    methods: {
        showAlert() {
            if (this.selectedCars.length === 1) {
                this.alertHeading = 'Fahrzeug löschen';
                this.alertParagraph = 'Möchten Sie das ausgewählte Fahrzeug wirklich löschen?';
                this.alertOkayButton = 'Löschen';
            }

            if (this.selectedCars.length > 1) {
                this.alertHeading = 'Fahrzeuge löschen';
                this.alertParagraph = 'Möchten Sie die ausgewählten Fahrzeuge wirklich löschen?';
                this.alertOkayButton = 'Löschen';
            }

            if (this.editCarId) {
                this.alertHeading = 'Fahrzeug bearbeiten';
                this.alertParagraph = 'Möchten Sie die Änderungen wirklich speichern?';
                this.alertOkayButton = 'Speichern';
            }
            this.isAlertVisible = true;
        },

        handleConfirmation() {
            if (this.carToDelete) {
                this.deleteCar();
            } if (this.editCarId) {
                this.saveCar(this.editCarId);
            }
            else {
                this.deleteCars();
            }
            this.isAlertVisible = false;
        },
        async loadItems(options) {
            this.loading = true;
            const { page = 1, itemsPerPage = 10, sortBy = [{ key: 'Kennzeichen' }], sortDesc = [false] } = options || {};

            try {
                const response = await axios.get('/api/cars', {
                    params: {
                        page,
                        itemsPerPage,
                        sortBy: sortBy.length > 0 ? sortBy[0].key : '',
                        sortDesc: sortDesc.length > 0 ? sortDesc[0] : true,
                    },
                });
                this.cars = response.data.items;
                this.totalItems = response.data.total;
            } catch (error) {
                console.error('Fehler beim Laden der Daten:', error);
            }
            this.loading = false;
        },
        confirmDeleteCars(kennzeichen) {
            this.carToDelete = kennzeichen;
            this.isAlertVisible = true;
            this.showAlert();
        },
        confirmEditCar(editCarId) {
            this.editCarId = editCarId;
            this.showAlert();
        },
        async deleteCar() {
            console.log('Attempting to delete car:', this.carToDelete);
            if (this.carToDelete) {
                try {
                    await axios.delete(`/api/cars/${this.carToDelete}`);
                    console.log('Car deleted successfully:', this.carToDelete);
                    this.cars = this.cars.filter(car => car.Kennzeichen !== this.carToDelete);
                    // this.loadItems(this.options);
                    this.carToDelete = null;
                } catch (error) {
                    console.error('Fehler beim Löschen des Fahrzeuges:', error.response?.data || error.message);
                } finally {
                    this.isAlertVisible = false;
                }
            } else {
                console.error('No car selected for deletion');
            }
        },

        async deleteCars() {
            if (this.selectedCars.length > 0) {
                try {
                    await axios.delete(`/api/cars`, {
                        data: { kennzeichen: this.selectedCars }
                    });
                    this.cars = this.cars.filter(car => !this.selectedCars.includes(car.Kennzeichen));
                    this.selectedCars = [];
                    this.$emit('carsDeleted');
                } catch (error) {
                    console.error('Fehler beim Löschen der Fahrzeuge:', error);
                }
            }
        },

        editCarDetails(car) {
            this.editCarId = car.Kennzeichen;
            this.editCar = { ...car };
        },
        saveCar(kennzeichen) {
            this.editCarId = null;
            this.editCar = {};
        },
    }
}
</script>

<style scoped>
.button-group {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-direction: row;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0px;
}

.spacer {
    flex-grow: 1;
}

.table-container {
    position: relative;
    width: 100%;
}

.scrollable-table {
    max-height: 800px;
    overflow-y: auto;
}

.custom-table {
    width: 100%;
}

.fixed-width {
    width: 200px;
}

.edit-field {
    padding: 10px 12px;
}

.edit-field input {
    padding: 8px;
    font-size: 1em;
    box-sizing: border-box;
    width: 100%;
}

.edit-button,
.delete-button {
    display: flex;
    justify-content: center;
    align-items: center;
}

.delete-button:hover {
    background-color: red !important;
}

.table {
    margin: 25px 0;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

.table-icon {
    width: 0.5vh;
}

@media only screen and (max-width: 600px) {
    .table-container {
        width: 50%;
        height: 50%;
    }

    .custom-table {
        width: 50%;
        height: 50%;
    }
}

@media only screen and (min-height: 1080px) {
    .table-container {
        height: 130%;
    }

    .custom-table {
        height: 130%;
    }
}

@media only screen and (min-height: 1440px) {
    .table-container {
        height: 150%;
    }

    .custom-table {
        height: 150%;
    }
}
</style>