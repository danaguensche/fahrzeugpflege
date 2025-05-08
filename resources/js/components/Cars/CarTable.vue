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
                <v-data-table-server :headers="headers" :items="cars" :server-items-length="totalItems"
                    :loading="loading" @update:options="loadItems" single-sort item-key="Kennzeichen" :sort-by="[]">

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
                { title: 'Kennzeichen', key: 'Kennzeichen', sortable: true },
                { title: 'Fahrzeugklasse', key: 'Fahrzeugklasse', sortable: true },
                { title: 'Automarke', key: 'Automarke', sortable: true },
                { title: 'Typ', key: 'Typ', sortable: true },
                { title: 'Farbe', key: 'Farbe', sortable: true },
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
                sortBy: [],
                sortDesc: [],
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
        console.log('Initial options:', JSON.stringify(this.options));
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

        handleConfirmation() {
            if (this.confirmAction) {
                this.confirmAction();
            }
            this.isAlertVisible = false;
        },

        async loadItems() {
            this.loading = true;

            try {
                const params = {
                    page: this.options.page,
                    itemsPerPage: this.options.itemsPerPage,
                    orderByNewest: true  // Standardmäßig nach "zuletzt hinzugefügt" sortieren
                };

                // Sortierung nur anwenden, wenn explizit in der Tabelle gesetzt
                if (this.options.sortBy && this.options.sortBy.length > 0) {
                    params.sortBy = this.options.sortBy[0];
                    params.sortDesc = this.options.sortDesc[0] ? 'true' : 'false';
                    params.orderByNewest = false;  // Eigene Sortierung deaktivieren wenn Tabellensortierung aktiv
                    console.log('Sortierung nach:', params.sortBy, 'absteigend:', params.sortDesc);
                }

                console.log('Request params:', params);

                const response = await axios.get('/api/cars', { params });
                console.log('API Response:', response.data);

                this.cars = response.data.items;
                this.totalItems = response.data.total;
            } catch (error) {
                console.error('Fehler beim Laden der Daten:', error);
                if (error.response) {
                    console.error('Response data:', error.response.data);
                }
            } finally {
                this.loading = false;
            }
        },

        async deleteCar() {
            if (this.carToDelete) {
                try {
                    await axios.delete(`/api/cars/${this.carToDelete}`);
                    console.log('Car deleted successfully:', this.carToDelete);
                    await this.loadItems();  // Tabelle nach dem Löschen neu laden
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
                    await this.loadItems();
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
    align-items: center;
    gap: 12px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding: 8px 0;
    margin-right: 20px;
}

.spacer {
    flex-grow: 1;
}

.table-container {
    position: relative;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    max-width: 100%;
    margin-right: 20px;
}

.scrollable-table {
    min-height: 90%;
    max-height: 90%;
    overflow-y: auto;
    background-color: #fff;
    width: 90vw;
    max-width: 100%;

}

.fixed-width {
    width: 150px;
    padding: 12px 16px;
}

.table-icon {
    width: 48px;
    text-align: center;
}

/* Tabellenstil */
:deep(.v-data-table) {
    border-collapse: collapse;
    width: 100%;
    table-layout: fixed;
}

:deep(.v-data-table th) {
    background-color: #f5f7fa;
    color: #2c3e50;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    padding: 16px;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

:deep(.v-data-table td) {
    border-bottom: 1px solid #edf2f7;
    padding: 14px 16px;
    color: #333;
}

:deep(.v-data-table tr:hover) {
    background-color: #f9fafc;
}

/* Button-Styling */
.refresh-button :deep(.v-btn),
.confirm-button :deep(.v-btn),
.cancel-button :deep(.v-btn),
.delete-button :deep(.v-btn) {
    text-transform: none;
    font-weight: 500;
    border-radius: 6px;
    padding: 0 16px;
    height: 40px;
    transition: all 0.2s;
}

.confirm-button :deep(.v-btn) {
    background-color: #4caf50;
    color: white;
}

.confirm-button :deep(.v-btn:hover) {
    background-color: #388e3c;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
}

.cancel-button :deep(.v-btn) {
    background-color: #f2f2f2;
    color: #333;
}

.cancel-button :deep(.v-btn:hover) {
    background-color: #e0e0e0;
}

.delete-button :deep(.v-btn) {
    background-color: #f44336;
    color: white;
}

.delete-button :deep(.v-btn:hover) {
    background-color: #d32f2f;
    box-shadow: 0 2px 8px rgba(244, 67, 54, 0.3);
}

.refresh-button :deep(.v-btn) {
    background-color: #2196f3;
    color: white;
}

.refresh-button :deep(.v-btn:hover) {
    background-color: #1976d2;
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
}

/* Icons in Tabelle */
:deep(.v-btn.v-btn--icon) {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    transition: all 0.2s;
}

:deep(.v-btn.v-btn--icon:hover) {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Icon-Farben */
:deep(.mdi-delete) {
    color: #f44336;
}

:deep(.mdi-pencil) {
    color: #2196f3;
}

:deep(.mdi-content-save) {
    color: #4caf50;
}

:deep(.v-text-field) {
    margin: 0;
    padding: 0;
}

:deep(.v-text-field .v-input__control) {
    min-height: 36px;
}

:deep(.v-checkbox) {
    margin: 0;
    padding: 0;
}

a {
    color: #1976d2;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

a:hover {
    color: #0d47a1;
    text-decoration: underline;
}

/* Loading-Anzeige */
:deep(.v-progress-circular) {
    margin: 16px auto;
    display: block;
}

/* Responsives Design */
@media (max-width: 960px) {
    .button-group {
        flex-wrap: wrap;
    }

    .fixed-width {
        width: auto;
    }
}
</style>