<template>
    <n-config-provider :locale="locale" :date-locale="dateLocale">
        <div>
            <div class="button-container">
                <n-button-group>
                    <n-popconfirm title="Sind Sie sicher, dass Sie die ausgewählten Kunden löschen möchten?"
                        @positive-click="deleteCars">
                        <template #trigger>
                            <n-button class="delete-car-button">Löschen</n-button>
                        </template>
                    </n-popconfirm>

                    <n-popconfirm title="Wollen Sie die Änderungen speichern?" @positive-click="saveChanges">
                        <template #trigger>
                            <n-button>Bestätigen</n-button>
                        </template>
                    </n-popconfirm>

                    <n-button @click="anotherAction">Verwerfen</n-button>
                </n-button-group>
            </div>

            <div class="table-container" :class="{ 'table-container-sidebar-opened': isSidebarOpen }">
                <div v-if="cars.length === 0">Laden...</div>
                <table v-else class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="select">Auswählen</th>
                            <th>Kennzeichen</th>
                            <th>Fahrzeugklasse</th>
                            <th>Automarke</th>
                            <th>Typ</th>
                            <th>Farbe</th>
                            <th>Löschen</th>
                            <th>Bearbeiten</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="car in cars" :key="car.Kennzeichen">
                            <td class="checkbox">
                                <Checkbox></Checkbox>
                            </td>
                            <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                                <input v-model="editCar.Kennzeichen" />
                            </td>
                            <td v-else>
                                <a href="">{{ car.Kennzeichen }}</a>
                            </td>
                            <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                                <input v-model="editCar.Fahrzeugklasse" />
                            </td>
                            <td v-else>{{ car.Fahrzeugklasse }}</td>
                            <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                                <input v-model="editCar.Automarke" />
                            </td>
                            <td v-else>{{ car.Automarke }}</td>
                            <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                                <input v-model="editCar.Typ" />
                            </td>
                            <td v-else>{{ car.Typ }}</td>
                            <td v-if="editCarId === car.Kennzeichen" class="edit-field">
                                <input v-model="editCar.Farbe" />
                            </td>
                            <td v-else>{{ car.Farbe }}</td>
                            <td class="table-icon">
                                <DeleteButton></DeleteButton>
                            </td>
                            <td class="table-icon">
                                <EditButton
                                    @click="editCarId === car.Kennzeichen ? saveCar(car.Kennzeichen) : editCarDetails(car)" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Pagination :currentPage="currentPage" :totalPages="totalPages" @page-changed="changePage"></Pagination>
    </n-config-provider>
</template>

<script>
import Checkbox from '../CommonSlots/Checkbox.vue';
import DeleteButton from '../CommonSlots/DeleteButton.vue';
import EditButton from '../CommonSlots/EditButton.vue';
import Pagination from '../CommonSlots/Pagination.vue';
import { NPopconfirm, NButton, NButtonGroup, NConfigProvider, deDE, dateDeDE } from 'naive-ui';

export default {
    name: "CarTable",
    components: {
        Checkbox,
        DeleteButton,
        EditButton,
        Pagination,
        NPopconfirm,
        NButton,
        NButtonGroup,
        NConfigProvider
    },
    props: {
        cars: {
            type: Array,
            required: true
        },
        editCarId: {
            type: String,
            default: null
        },
        editCar: {
            type: Object,
            default: () => ({})
        },
        currentPage: {
            type: Number,
            required: true
        },
        totalPages: {
            type: Number,
            required: true
        }
    },

    data() {
        return {
            selectedCars: [],
            showDeleteButton: false,
            locale: deDE,
            dateLocale: dateDeDE
        };
    },
    methods: {
        updateSelectedCars(carId, isChecked) {
            if (isChecked) {
                this.selectedCars.push(carId);
            } else {
                this.selectedCars = this.selectedCars.filter(id => id !== carId);
            }
        },

        deleteCars() {
            console.log('Deleting cars:', this.selectedCars);

        },
        editCarDetails(car) {
            this.$emit('edit-car', car);
        },
        saveCar(kennzeichen) {
            this.$emit('save-car', kennzeichen);
        },
        changePage(page) {
            this.$emit('page-changed', page);
        }
    }
}
</script>

<style scoped>
.button-container {
    display: flex;
    justify-content: flex-end;
    margin-right: 3px;
    margin-bottom: -15px;
}

.table-container {
    position: relative;
    width: 1300px;
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

.checkbox {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.edit-button,
.delete-button {
    display: flex;
    justify-content: center;
    align-items: center;
    transform: scale(0.25);
    margin-top: -50px;
}

.delete-button {
    margin-left: -15px;
}

.table {
    border-collapse: separate;
    margin: 25px 0;
    font-size: 0.9em;
    width: 100%;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

.table-icon {
    width: 0.5vh;
}

.table thead tr {
    background-color: darkslategray;
    color: #ffffff;
    text-align: left;
}

.table th,
.table td {
    padding: 12px 15px;
}

.table th {
    height: 45px;
}

.table tbody tr {
    border-bottom: 1px solid #dddddd;
}

.table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.table tbody tr.active-row {
    font-weight: bold;
    color: #009879;
}
</style>