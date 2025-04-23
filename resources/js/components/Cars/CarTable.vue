<template>
    <div class="component">
        <div class="header">
            <RefreshButton class="refresh-button" @refresh="loadItems"></RefreshButton>
            <div class="spacer"></div>

            <div class="button-group">
                <ConfirmButton class="confirm-button" @click="confirmEditCar" :disabled="!editCarId">Bestätigen
                </ConfirmButton>
                <CancelButton class="cancel-button" @click="cancelEdit">Abbrechen</CancelButton>
                <DeleteButton class="delete-button" :disabled="selectedCars.length === 0"
                    @click="confirmDeleteSelectedCars">
                    Löschen
                </DeleteButton>
            </div>

        </div>
        <div class="table-container">
            <div v-if="loading"><v-progress-circular indeterminate></v-progress-circular></div>
            <div class="scrollable-table">
                <v-data-table-server :headers="headers" :items="cars" :options.sync="options"
                    :server-items-length="totalItems" :loading="loading" @update:options="loadItems" fixed-header>
                    <template v-slot:item="{ item }">
                        <tr>
                            <td class="checkbox fixed-width">
                                <v-checkbox v-model="selectedCars" :value="item.Kennzeichen"></v-checkbox>
                            </td>
                            <td v-for="field in fields" :key="field" class="fixed-width">
                                <template v-if="editCarId === item.Kennzeichen">
                                    <v-text-field v-model="editCar[field]" :rules="getFieldRules(field)">
                                    </v-text-field>
                                </template>
                                <template v-else>
                                    <a v-if="field === 'Kennzeichen'"
                                        :href="`/fahrzeuge/fahrzeugdetails/${item[field] ? item[field].replace(/\s/g, '+') : ''}`">
                                        {{ item[field] || '' }}
                                    </a>
                                    <span v-else>{{ item[field] || '' }}</span>
                                </template>
                            </td>
                            <td class="table-icon fixed-width">
                                <v-btn icon class="delete-button" variant="plain"
                                    @click="confirmDeleteCar(item.Kennzeichen)">
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </td>
                            <td class="table-icon fixed-width">
                                <v-btn variant="plain" icon
                                    @click="editCarId === item.Kennzeichen ? saveCar() : editCarDetails(item)">
                                    <v-icon>{{ editCarId === item.Kennzeichen ? 'mdi-content-save' : 'mdi-pencil'
                                        }}</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                    </template>
                </v-data-table-server>
            </div>
        </div>
        <VuetifyAlert v-model="isAlertVisible" maxWidth="500" alertTypeClass="alertTypeConfirmation"
            :alertHeading="alertHeading" :alertParagraph="alertParagraph" :alertOkayButton="alertOkayButton"
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
                { title: 'Löschen', key: 'delete', sortable: false },
                { title: 'Bearbeiten', key: 'edit', sortable: false }
            ],
            fields: ["Kennzeichen", "Fahrzeugklasse", "Automarke", "Typ", "Farbe"],
            editCarId: null,
            editCar: {},
            loading: false,
            totalItems: 0,
            alertHeading: '',
            alertParagraph: '',
            alertOkayButton: '',
            options: {
                page: 1,
                itemsPerPage: 10,
                sortBy: [{ key: 'Kennzeichen' }],
                sortDesc: [false],
            },
            confirmAction: null
        };
    },

    computed: {
        currentPage() {
            return this.options.page;
        },
        totalPages() {
            return Math.ceil(this.totalItems / this.options.itemsPerPage);
        },
        isEditing() {
            return this.editCarId !== null;
        },
    },

    mounted() {
        this.loadItems();
    },

    methods: {
        getFieldRules(field) {
            if (field === 'Fahrzeugklasse') {
                return [
                    function (value) {
                        return !!value || 'Fahrzeugklasse ist erforderlich';
                    },
                    function (value) {
                        return typeof value === 'int' || 'Muss eine Zahl sein';
                    }
                ];
            } else if (field === 'Kennzeichen') {
                return [
                    function (value) {
                        return !!value || 'Kennzeichen ist erforderlich';
                    },
                    function (value) {
                        return typeof value === 'string' || 'Muss eine Zeichenfolge sein';
                    }
                ];
            } else {
                return [];
            }
        },

        showAlert(heading, paragraph, okayButton, action) {
            this.alertHeading = heading;
            this.alertParagraph = paragraph;
            this.alertOkayButton = okayButton;
            this.confirmAction = action;
            this.isAlertVisible = true;
        },

        confirmDeleteCar(kennzeichen) {
            this.carToDelete = kennzeichen;
            this.showAlert(
                'Fahrzeug löschen',
                'Möchten Sie das ausgewählte Fahrzeug wirklich löschen?',
                'Löschen',
                this.deleteCar
            );
        },

        confirmDeleteSelectedCars() {
            if (this.selectedCars.length > 0) {
                const message = this.selectedCars.length === 1
                    ? 'Möchten Sie das ausgewählte Fahrzeug wirklich löschen?'
                    : 'Möchten Sie die ausgewählten Fahrzeuge wirklich löschen?';

                const heading = this.selectedCars.length === 1
                    ? 'Fahrzeug löschen'
                    : 'Fahrzeuge löschen';

                this.showAlert(
                    heading,
                    message,
                    'Löschen',
                    this.deleteCars
                );
            }
        },

        async confirmEditCar() {
            try {
                await axios.put(`/api/cars/${this.editCarId}`, this.editCar);
                this.editCarId = null;
                this.editCar = {};
                await this.loadItems();
            } catch (error) {
                console.error('Error data:', error.response?.data);
                this.$emit('show-error', 'Fehler beim Speichern des Fahrzeuges');
            }
        },

        confirmEdit() {
            if (this.editCustomerId) {
                this.showAlert(
                    'Kunde bearbeiten',
                    'Möchten Sie die Änderungen wirklich speichern?',
                    'Speichern',
                    this.updateCustomer
                );
            }
        },

        handleConfirmation() {
            if (this.confirmAction) {
                this.confirmAction();
            }
            this.isAlertVisible = false;
        },

        async loadItems() {
            this.loading = true;

            try {
                const response = await axios.get('/api/cars');
                this.cars = response.data.items;
                this.totalItems = response.data.total;
            } catch (error) {
                console.error('Fehler beim Laden der Daten:', error);
            }
            this.loading = false;
        },

        async deleteCar() {
            if (this.carToDelete) {
                try {
                    await axios.delete(`/api/cars/${this.carToDelete}`);
                    console.log('Car deleted successfully:', this.carToDelete);
                    this.cars = this.cars.filter(car => car.Kennzeichen !== this.carToDelete);
                    this.selectedCars = this.selectedCars.filter(kennzeichen => kennzeichen !== this.carToDelete);
                    this.carToDelete = null;
                } catch (error) {
                    console.error('Fehler beim Löschen des Fahrzeuges:', error.response?.data || error.message);
                }
            }
        },

        async deleteCars() {
            if (this.selectedCars.length > 0) {
                try {
                    await axios.delete(`/api/cars`, {
                        data: { ids: this.selectedCars }
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

        async saveCar() {
            try {
                await axios.put(`/api/cars/${this.editCarId}`, this.editCar);
                this.cancelEdit();
                await this.loadItems();
            } catch (error) {
                console.error('Fehler beim Speichern des Fahrzeuges:', error.response?.data || error.message);
            }
        },

        cancelEdit() {
            this.editCarId = null;
            this.editCar = {};
        }
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